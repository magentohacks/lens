<?php

namespace BoostMyShop\AdvancedStock\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class CatalogProductAfterSave implements ObserverInterface
{

    protected $_warehouseItemFactory;
    protected $_stockMovementFactory;
    protected $_backendAuthSession;
    protected $_logger;

    /**
     * @param StockIndexInterface $stockIndex
     * @param StockConfigurationInterface $stockConfiguration
     * @param StockRegistryInterface $stockRegistry
     * @param StockItemRepositoryInterface $stockItemRepository
     */
    public function __construct(
        \BoostMyShop\AdvancedStock\Helper\Logger $logger,
        \BoostMyShop\AdvancedStock\Model\Warehouse\ItemFactory $warehouseItemFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \BoostMyShop\AdvancedStock\Model\StockMovementFactory $stockMovementFactory
    ) {
        $this->_warehouseItemFactory = $warehouseItemFactory;
        $this->_stockMovementFactory = $stockMovementFactory;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_logger = $logger;
    }

    /**
     * Saving product inventory data. Product qty calculated dynamically.
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if (!$product)
            return;

        if (!$product->getOrigData('entity_id')) {
            $this->_warehouseItemFactory->create()->createRecord($product->getId());

            if (isset($product->getData()['stock_data']['qty']))
            {
                $qty = $product->getData()['stock_data']['qty'];
                $this->_stockMovementFactory->create()->updateProductQuantity($product->getId(), 1, 0, $qty, 'Product creation', $this->getUserId());
            }
        }

        return $this;
    }


    protected function getUserId()
    {
        $userId = null;
        if ($this->_backendAuthSession->getUser())
            $userId = $this->_backendAuthSession->getUser()->getId();
        return $userId;
    }
}
