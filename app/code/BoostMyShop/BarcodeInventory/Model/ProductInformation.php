<?php

namespace BoostMyShop\BarcodeInventory\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;

class ProductInformation
{
    protected $_productRepository;
    protected $_objectManager;
    protected $_stockState;
    protected $_collectionFactory;

    public function __construct(ProductRepositoryInterface $productRepository,
                                ObjectManagerInterface $om,
                                \Magento\CatalogInventory\Api\StockStateInterface $stockState,
                                \Magento\Catalog\Model\ResourceModel\Product\Collection $collectionFactory
                                ) {
        $this->_objectManager = $om;
        $this->_productRepository = $productRepository;
        $this->_stockState = $stockState;
        $this->_collectionFactory = $collectionFactory;

    }


    public function getJsonDataForBarcode($barcode)
    {
        $productId = $this->getIdFromBarcode($barcode);
        if (!$productId)
            throw new \Exception('No product found with barcode '.$barcode);

        $product = $this->_productRepository->getById($productId);

        $data['id'] = $product->getId();
        $data['name'] = $product->getName();
        $data['sku'] = $product->getSku();
        $data['image_url'] = $this->getImage($product);
        $data['barcode'] = $barcode;

        $data['qty'] = $this->_stockState->getStockQty($product->getId());

        return $data;
    }

    protected function getIdFromBarcode($barcode)
    {
        $collection = $this->_collectionFactory;
        $collection->addAttributeToFilter($this->getBarcodeAttribute(), $barcode);

        foreach($collection as $item)
            return $item->getId();

        return false;
    }

    protected function getImage($product)
    {
        $helper = $this->_objectManager->get('\Magento\Catalog\Helper\Product');
        return $helper->getImageUrl($product);
    }

    protected function getBarcodeAttribute()
    {
        $config = $this->_objectManager->get('\BoostMyShop\BarcodeInventory\Model\Config\BarcodeInventory');
        return $config->getSetting('general/barcode_attribute');
    }
}
