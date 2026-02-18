<?php

namespace BoostMyShop\AdvancedStock\Plugin\CatalogInventory\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Stock
{
    protected $scopeConfig;
    protected $_logger;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \BoostMyShop\AdvancedStock\Helper\Logger $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_logger = $logger;
    }

    //rewrite this function to prevent duplicate records in product collection if several records in cataloginventory_stock_item are available
    public function aroundAddInStockFilterToCollection(\Magento\CatalogInventory\Helper\Stock $subject, $proceed, $collection)
    {
        $websiteId = 0;

        $manageStock = $this->scopeConfig->getValue(
            \Magento\CatalogInventory\Model\Configuration::XML_PATH_MANAGE_STOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $cond = [
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=1 AND {{table}}.is_in_stock=1',
            '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=0'
        ];

        if ($manageStock) {
            $cond[] = '{{table}}.use_config_manage_stock = 1 AND {{table}}.is_in_stock=1';
        } else {
            $cond[] = '{{table}}.use_config_manage_stock = 1';
        }

        $collection->joinField(
            'inventory_in_stock',
            'cataloginventory_stock_item',
            'is_in_stock',
            'product_id=entity_id',
            '((' . join(') OR (', $cond) . ')) and website_id = '.$websiteId
        );

        $this->_logger->log('aroundAddInStockFilterToCollection ', \BoostMyShop\AdvancedStock\Helper\Logger::kLogInventory);
    }

}