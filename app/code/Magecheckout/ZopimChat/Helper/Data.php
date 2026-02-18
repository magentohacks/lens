<?php

namespace Magecheckout\ZopimChat\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_GENERAL_ENABLED = 'zopimchat/general/is_enabled';
    const XML_PATH_GENERAL_EMBED_WIDGET = 'zopimchat/general/embed_widget';
    protected $storeManager;
    protected $objectManager;

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager  = $storeManager;
        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isEnabled($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GENERAL_ENABLED, $storeId);

    }

    public function getEmbedWidget($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GENERAL_EMBED_WIDGET, $storeId);
    }


}