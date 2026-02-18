<?php

namespace BoostMyShop\OrderPreparation\Model;

class Product
{
    protected $_configFactory = null;
    protected $_productHelper = null;
    protected $_dir = null;

    /*
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\BoostMyShop\OrderPreparation\Model\ConfigFactory $configFactory,
                                \Magento\Catalog\Model\ProductFactory $productFactory,
                                \Magento\Framework\App\Filesystem\DirectoryList $dir,
                                \Magento\Catalog\Helper\Product $productHelper

){
        $this->_configFactory = $configFactory;
        $this->_productFactory = $productFactory;
        $this->_productHelper = $productHelper;
        $this->_dir = $dir;
    }

    public function getLocation($productId, $warehouseId)
    {
        $attributeCode = $this->_configFactory->create()->getLocationAttribute();
        if ($attributeCode)
        {
            $product = $this->_productFactory->create()->load($productId);
            return $product->getData($attributeCode);
        }
        return "";
    }

    public function setLocation($productId)
    {

    }

    public function getImageUrl($productId)
    {
        $product = $this->_productFactory->create()->load($productId);
        return $this->_productHelper->getImageUrl($product);
    }

    public function getImagePath($productId)
    {
        $product = $this->_productFactory->create()->load($productId);
        $fullPath = '/'.'catalog'.'/'.'product'.$product->getImage();
        return $fullPath;
    }

    public function getBarcode($productId)
    {
        $attributeCode = $this->_configFactory->create()->getBarcodeAttribute();
        if ($attributeCode)
        {
            $product = $this->_productFactory->create()->load($productId);
            return $product->getData($attributeCode);
        }
        return "";
    }

}