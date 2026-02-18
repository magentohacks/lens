<?php

namespace BoostMyShop\Supplier\Model\ResourceModel\Replenishment;


class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{

    public function init()
    {
        $this->addAttributeToSelect('name');
        $this->addAttributeToSelect('status');
        $this->addAttributeToSelect('thumbnail');
        $this->addAttributeToSelect('qty_to_receive');
        $this->addFieldToFilter('type_id', array('in' => array('simple')));

        $productIds = array_merge($this->getBackorderProductIds(), $this->getLowStockProductIds());
        if (count($productIds) > 0)
            $this->addFieldToFilter('entity_id', array('in' => $productIds));

        $this->joinAdditionalFields();

        return $this;
    }

    public function joinAdditionalFields()
    {
        $this->getSelect()->join($this->getTable('cataloginventory_stock_item'), 'product_id = entity_id and website_id = 0');

        $this->getSelect()->columns(['qty_for_backorder' => new \Zend_Db_Expr('if (qty < 0, -qty, 0)')]);

        $defaultConfigNotifyStockQty = 1;
        $expr = 'if (use_config_notify_stock_qty = 1, '.$defaultConfigNotifyStockQty.' - if(qty > 0, qty, 0), notify_stock_qty - if(qty > 0, qty, 0))';
        $this->getSelect()->columns(['qty_for_low_stock' => new \Zend_Db_Expr($expr)]);
    }

    /**
     * Post inject qty_to_order
     * Todo : use regular collection sql query to allow future filter / sort on this value
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this as $product) {
            $value = $product->getqty_for_backorder() + $product->getqty_for_low_stock() - $product->getqty_to_receive();
            $product->setData('qty_to_order', max($value, 0));
        }

        return $this;
    }


    public function getBackorderProductIds()
    {
        $mySelect = clone $this->getSelect();
        $mySelect->reset()->from($this->getTable('cataloginventory_stock_item'), ['product_id'])->where("qty < 0");
        return $this->getConnection()->fetchCol($mySelect);
    }

    public function getLowStockProductIds()
    {
        //todo : retrieve value from configuration
        $notifyStockQuantity = 1;

        $mySelect = clone $this->getSelect();
        $mySelect->reset()->from($this->getTable('cataloginventory_stock_item'), ['product_id'])->where("(use_config_notify_stock_qty = 1 and qty < ".$notifyStockQuantity.") OR (use_config_notify_stock_qty = 0 and qty < notify_stock_qty)");
        $ids = $this->getConnection()->fetchCol($mySelect);
        return $ids;
    }

    public function addProductFilter($productId)
    {
        $this->addFieldToFilter('entity_id', $productId);
        return $this;
    }

    protected function _getSelectCountSql($select = null, $resetLeftJoins = true)
    {
        $this->_renderFilters();

        if(count($this->getSelect()->getPart('group')) > 0) {
            $countSelect = clone $this->getSelect();
            $countSelect->reset('order');
            $countSelect->reset('limitcount');
            $countSelect->reset('limitoffset');
            $countSelect->reset('columns');
            $countSelect->reset('group');
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart('group');
            $countSelect->columns("COUNT(DISTINCT ".implode(", ", $group).")");
        }
        else
        {
            $countSelect = is_null($select) ? $this->_getClearSelect() : $this->_buildClearSelect($select);
            $countSelect->columns('COUNT(DISTINCT e.entity_id)');
            if ($resetLeftJoins) {
                $countSelect->resetJoinLeft();
            }
        }

        return $countSelect;
    }

}
