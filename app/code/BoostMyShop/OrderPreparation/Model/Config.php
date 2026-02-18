<?php

namespace BoostMyShop\OrderPreparation\Model;

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
        return $this->_scopeConfig->getValue('orderpreparation/'.$path, 'store', $storeId);
    }

    public function getBarcodeAttribute()
    {
        return $this->_scopeConfig->getValue('orderpreparation/attributes/barcode_attribute');
    }

    public function getLocationAttribute()
    {
        return $this->_scopeConfig->getValue('orderpreparation/attributes/shelflocation_attribute');
    }

    public function getOrderStatusesForTab($tab)
    {
        $statuses = explode(',', $this->_scopeConfig->getValue('orderpreparation/status_mapping/'.$tab));
        return $statuses;
    }

    public function getAllowPartialPacking()
    {
        return $this->_scopeConfig->getValue('orderpreparation/packing/allow_partial');
    }

    public function getCreateInvoice()
    {
        return $this->_scopeConfig->getValue('orderpreparation/packing/create_invoice');
    }

    public function getCreateShipment()
    {
        return $this->_scopeConfig->getValue('orderpreparation/packing/create_shipment');
    }

    public function includeInvoiceInDownloadDocuments()
    {
        return $this->_scopeConfig->getValue('orderpreparation/download/invoice');
    }

    public function includeShipmentInDownloadDocuments()
    {
        return $this->_scopeConfig->getValue('orderpreparation/download/shipment');
    }

    public function getPdfPickingLayout()
    {
        return $this->_scopeConfig->getValue('orderpreparation/picking/pdf_layout');
    }


}