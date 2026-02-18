<?php

namespace BoostMyShop\Rma\Model;

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
        return $this->_scopeConfig->getValue('rma/'.$path, 'store', $storeId);
    }

    public function getGlobalSetting($path, $storeId = 0)
    {
        return $this->_scopeConfig->getValue($path, 'store', $storeId);
    }

    public function getReasons($storeId = 0)
    {
        $string = $this->getSetting('general/reasons', $storeId);
        $reasons = [];
        foreach(explode("\n", $string) as $line)
        {
            if (count(explode(':', $line)) == 2) {
                list($code, $label) = explode(':', $line);
                $reasons[$code] = $label;
            }
        }
        return $reasons;
    }

    public function getReasonLabel($reason, $storeId=0)
    {
        foreach($this->getReasons() as $k => $v)
        {
            if ($k == $reason)
                return $v;
        }
    }

    public function getRequests($storeId = 0)
    {
        $string = $this->getSetting('general/requests', $storeId);
        $requests = [];
        foreach(explode("\n", $string) as $line)
        {
            if (count(explode(':', $line)) == 2) {
                list($code, $label) = explode(':', $line);
                $requests[$code] = $label;
            }
        }
        return $requests;
    }

    public function getRequestLabel($request, $storeId=0)
    {
        foreach($this->getRequests() as $k => $v)
        {
            if ($k == $request)
                return $v;
        }
    }

    public function getPdfInstructions($storeId = 0)
    {
        return $this->getSetting('general/instructions', $storeId);
    }

    public function canRequestReturn($storeId = 0)
    {
        return $this->getSetting('front/request', $storeId);
    }

    public function getAllowedStatusesForReturnRequest($storeId = 0)
    {
        return explode(',', $this->getSetting('front/allowed_statuses', $storeId));
    }

    public function getAdminNotificationEmail($storeId = 0)
    {
        return $this->getSetting('admin_notification/admin_notification_email', $storeId);
    }

    public function getAutomaticCustomerNotification($storeId = 0)
    {
        return $this->getSetting('notification/automatic_notification', $storeId);
    }

    public function getCompanyName($storeId = 0)
    {
        return $this->getGlobalSetting('design/head/default_title', $storeId);
    }

}