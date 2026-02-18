<?php

namespace BoostMyShop\AdvancedStock\Model\ResourceModel\MassStockEditor;


class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\Product', 'Magento\Catalog\Model\ResourceModel\Product');
        $this->setRowIdFieldName('mse_id');
        $this->_initTables();
    }

    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }

    /**
     * Join fields to entity
     *
     * @return $this
     */
    protected function _joinFields()
    {
        $this->addAttributeToSelect('name')->addAttributeToSelect('sku')->addAttributeToSelect('status');

        /*
        $this->getSelect()->joinLeft(
            ['w' => $this->getTable('bms_advancedstock_warehouse')],
            'true',
            ['*']
        );
        */

        $this->getSelect()->join(
            ['wi' => $this->getTable('bms_advancedstock_warehouse_item')],
            '(wi_product_id = e.entity_id)',
            [
                'wi_product_id',
                'wi_warehouse_id',
                'wi_physical_quantity',
                'wi_available_quantity',
                'wi_shelf_location',
                'wi_quantity_to_ship',
                'wi_warning_stock_level',
                'wi_use_config_warning_stock_level',
                'wi_ideal_stock_level',
                'wi_use_config_ideal_stock_level',
                'concat(wi_product_id, \'_\', wi_warehouse_id) as mse_id',
            ]
        );

        return $this;
    }

    /**
     * Set order to attribute
     *
     * @param string $attributea
     * @param string $dir
     * @return $this
     */
    public function setOrder($attribute, $dir = 'DESC')
    {
        switch ($attribute) {
            case 'wi_warehouse_id':
            case 'wi_physical_quantity':
            case 'wi_quantity_to_ship':
            case 'wi_available_quantity':
            case 'wi_shelf_location':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;
            default:
                parent::setOrder($attribute, $dir);
                break;
        }
        return $this;
    }

    /**
     * Add attribute to filter
     *
     * @param AbstractAttribute|string $attribute
     * @param array|null $condition
     * @param string $joinType
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        switch ($attribute) {
            case 'wi_warehouse_id':
            case 'wi_physical_quantity':
            case 'wi_quantity_to_ship':
            case 'wi_available_quantity':
            case 'wi_shelf_location':
                $conditionSql = $this->_getConditionSql($attribute, $condition);
                $this->getSelect()->where($conditionSql);
                break;
            default:
                parent::addAttributeToFilter($attribute, $condition, $joinType);
                break;
        }
        return $this;
    }

}
