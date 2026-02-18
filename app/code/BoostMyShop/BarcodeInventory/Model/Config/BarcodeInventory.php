<?php

namespace BoostMyShop\BarcodeInventory\Model\Config;

class BarcodeInventory
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

    public function getSetting($path)
    {
        return $this->_scopeConfig->getValue('barcodeinventory/'.$path);
    }
}