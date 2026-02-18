<?php

namespace BoostMyShop\AdvancedStock\Plugin\Supplier\Model\ResourceModel\Replenishment;


class Collection
{
    protected $_config;

    public function __construct(
        \BoostMyShop\AdvancedStock\Model\Config $config
    ){
        $this->_config = $config;
    }


    public function aroundGetBackorderProductIds(\BoostMyShop\Supplier\Model\ResourceModel\Replenishment\Collection $subject, $proceed)
    {
        $mySelect = clone $subject->getSelect();
        $mySelect->reset()->from($subject->getTable('bms_advancedstock_warehouse_item'), ['wi_product_id'])->where("wi_quantity_to_ship > wi_physical_quantity");
        return $subject->getConnection()->fetchCol($mySelect);
    }

    public function aroundGetLowStockProductIds(\BoostMyShop\Supplier\Model\ResourceModel\Replenishment\Collection $subject, $proceed)
    {
        $defaultWarningStockLevel = $this->_config->getDefaultWarningStockLevel();

        $mySelect = clone $subject->getSelect();
        $mySelect->reset()->from($subject->getTable('bms_advancedstock_warehouse_item'), ['wi_product_id'])->where("wi_available_quantity < if(wi_use_config_warning_stock_level = 1, ".$defaultWarningStockLevel.", wi_warning_stock_level)");
        $productIds = $subject->getConnection()->fetchCol($mySelect);

        return $productIds;
    }

    public function aroundJoinAdditionalFields(\BoostMyShop\Supplier\Model\ResourceModel\Replenishment\Collection $subject, $proceed)
    {
        $defaultWarningStockLevel = $this->_config->getDefaultWarningStockLevel();
        $defaultIdealStockLevel = $this->_config->getDefaultIdealStockLevel();

        $subject->getSelect()->join($subject->getTable('bms_advancedstock_warehouse_item'), 'wi_product_id = entity_id');
        $subject->getSelect()->columns(['qty_for_backorder' => new \Zend_Db_Expr('SUM(if(wi_quantity_to_ship > wi_physical_quantity, wi_quantity_to_ship - wi_physical_quantity, 0))')]);

        $expr = 'SUM(if(wi_available_quantity < if (wi_use_config_warning_stock_level, '.$defaultWarningStockLevel.', wi_warning_stock_level), if (wi_use_config_ideal_stock_level, '.$defaultIdealStockLevel.', wi_ideal_stock_level) - wi_available_quantity, 0))';
        $subject->getSelect()->columns(['qty_for_low_stock' => new \Zend_Db_Expr($expr)]);

        $subject->getSelect()->group('sku');

        return $this;
    }


}
