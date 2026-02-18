<?php

namespace BoostMyShop\OrderPreparation\Model\ResourceModel\InProgress\Item;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\OrderPreparation\Model\InProgress\Item', 'BoostMyShop\OrderPreparation\Model\ResourceModel\InProgress\Item');
    }

    public function addOrderFilter($orderId)
    {
        $this->getSelect()->where("ipi_order_id = ".$orderId);
        return $this;
    }

    public function joinOrderItem()
    {
        $this->getSelect()->join($this->getTable('sales_order_item'), 'ipi_order_item_id = item_id');
        return $this;
    }

    public function deleteForOrder($orderId)
    {
        $this->getConnection()->delete($this->getTable('bms_orderpreparation_inprogress_item'), 'ipi_order_id = '.$orderId);
        return $this;
    }

}
