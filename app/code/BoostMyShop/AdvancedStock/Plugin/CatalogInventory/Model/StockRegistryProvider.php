<?php

namespace BoostMyShop\AdvancedStock\Plugin\CatalogInventory\Model;

use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\CatalogInventory\Api\StockRepositoryInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Api\StockStatusRepositoryInterface;
use Magento\CatalogInventory\Api\Data\StockInterfaceFactory;
use Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory;
use Magento\CatalogInventory\Api\Data\StockStatusInterfaceFactory;
use Magento\CatalogInventory\Api\StockCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockStatusCriteriaInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;

class StockRegistryProvider extends \Magento\CatalogInventory\Model\StockRegistryProvider
{
    protected $_logger;

    public function __construct(
        StockRepositoryInterface $stockRepository,
        StockInterfaceFactory $stockFactory,
        StockItemRepositoryInterface $stockItemRepository,
        StockItemInterfaceFactory $stockItemFactory,
        StockStatusRepositoryInterface $stockStatusRepository,
        StockStatusInterfaceFactory $stockStatusFactory,
        StockCriteriaInterfaceFactory $stockCriteriaFactory,
        StockItemCriteriaInterfaceFactory $stockItemCriteriaFactory,
        StockStatusCriteriaInterfaceFactory $stockStatusCriteriaFactory,
        \BoostMyShop\AdvancedStock\Helper\Logger $logger
    ) {
        $this->_logger = $logger;

        parent::__construct($stockRepository, $stockFactory, $stockItemRepository, $stockItemFactory, $stockStatusRepository, $stockStatusFactory, $stockCriteriaFactory,$stockItemCriteriaFactory,$stockStatusCriteriaFactory);
    }

    //tweak method to consider scope (as mageto should do !)
    public function aroundGetStockItem(\Magento\CatalogInventory\Model\StockRegistryProvider $subject, $proceed, $productId, $scopeId)
    {
        $key = $scopeId . '/' . $productId;
        if (!isset($this->stockItems[$key])) {
            $criteria = $this->stockItemCriteriaFactory->create();
            $criteria->setProductsFilter($productId);
            $criteria->setScopeFilter($scopeId);    //added line
            $collection = $this->stockItemRepository->getList($criteria);
            $stockItem = current($collection->getItems());
            if ($stockItem && $stockItem->getItemId()) {
                $this->stockItems[$key] = $stockItem;
            } else {
                $this->_logger->log('Stock item for scope '.$key.' does not exist, create it !', \BoostMyShop\AdvancedStock\Helper\Logger::kLogInventory);
                $stockItem = $this->createStockItemEntry($productId, $scopeId);   //second hack
                $this->stockItems[$key] = $stockItem;
            }
        }
        $this->_logger->log('Return stock item for scope '.$key.' (id:'.$this->stockItems[$key]->getId().')', \BoostMyShop\AdvancedStock\Helper\Logger::kLogInventory);

        return $this->stockItems[$key];
    }

    /**
     * Create a stock item entry, based on the default one
     *
     * @param $productId
     * @param $scopeId
     */
    protected function createStockItemEntry($productId, $scopeId)
    {
        $criteria = $this->stockItemCriteriaFactory->create();
        $criteria->setProductsFilter($productId);
        $collection = $this->stockItemRepository->getList($criteria);
        $stockItem = current($collection->getItems());

        if (!$stockItem)
            $stockItem = $this->stockItemFactory->create();

        if ($stockItem)
        {
            $stockItem->setproduct_id($productId);
            $stockItem->setwebsite_id($scopeId);
            $stockItem->unsetData('item_id');
            $stockItem->setData('qty', 0);
        }

        return $stockItem;
    }

}