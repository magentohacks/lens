<?php

namespace BoostMyShop\Supplier\Model\ResourceModel\Transit;


class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{

    public function init()
    {
        $this->addAttributeToSelect('name');
        $this->addAttributeToSelect('qty_to_receive');
        $this->addFieldToFilter('qty_to_receive', ['gt' => 0]);

        $this->getSelect()->join($this->getTable('bms_purchase_order_product'), 'e.entity_id = pop_product_id');
        $this->getSelect()->join($this->getTable('bms_purchase_order'), 'pop_po_id = po_id', new \Zend_Db_Expr('MIN(po_eta) as eta'));
        $this->getSelect()->where('po_status = "'.\BoostMyShop\Supplier\Model\Order\Status::expected.'"');
        $this->getSelect()->where('pop_qty > pop_qty_received');

        $this->getSelect()->group('e.entity_id');

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

    public function setOrder($attribute, $dir = 'DESC')
    {
        switch ($attribute) {
            case 'eta':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;
            default:
                parent::setOrder($attribute, $dir);
                break;
        }
        return $this;
    }
}
