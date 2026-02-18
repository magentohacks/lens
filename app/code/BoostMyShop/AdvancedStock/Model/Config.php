<?php

namespace BoostMyShop\AdvancedStock\Model;

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
        return $this->_scopeConfig->getValue('advancedstock/'.$path, 'store', $storeId);
    }

    public function getPendingOrderStatuses()
    {
        return explode(',', $this->_scopeConfig->getValue('advancedstock/opened_orders/opened_orders_statuses'));
    }

    public function getBarcodeAttribute()
    {
        return $this->_scopeConfig->getValue('advancedstock/attributes/barcode_attribute');
    }

    public function getSalesHistoryRanges()
    {
        return explode(',', $this->_scopeConfig->getValue('advancedstock/stock_level/history_ranges'));
    }

    public function displayStocksOnFrontEnd()
    {
        return $this->_scopeConfig->getValue('advancedstock/frontend/display_stocks');
    }

    public function getDefaultWarningStockLevel()
    {
        return $this->_scopeConfig->getValue('advancedstock/stock_level/default_warning');
    }

    public function getDefaultIdealStockLevel()
    {
        return $this->_scopeConfig->getValue('advancedstock/stock_level/default_ideal');
    }

    public function getDecreaseStockWhenOrderIsPlaced()
    {
        return $this->_scopeConfig->getValue('cataloginventory/options/can_subtract');
    }

    public function canBackInStock()
    {
        return $this->_scopeConfig->getValue('cataloginventory/options/can_back_in_stock');
    }

}