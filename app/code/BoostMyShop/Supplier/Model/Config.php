<?php

namespace BoostMyShop\Supplier\Model;

class Config
{
    /**
     * Core store config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /*
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig){
        $this->_scopeConfig = $scopeConfig;
    }

    public function getSetting($path, $storeId = 0)
    {
        return $this->_scopeConfig->getValue('supplier/'.$path, 'store', $storeId);
    }

    public function getGlobalSetting($path, $storeId = 0)
    {
        return $this->_scopeConfig->getValue($path, 'store', $storeId);
    }

    public function getBarcodeAttribute()
    {
        return $this->_scopeConfig->getValue('supplier/general/barcode_attribute');
    }

    public function getNotifyStockQuantity()
    {
        return $this->_scopeConfig->getValue('cataloginventory/item_options/notify_stock_qty');
    }

}