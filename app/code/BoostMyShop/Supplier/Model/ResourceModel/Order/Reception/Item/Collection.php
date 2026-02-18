<?php

namespace BoostMyShop\Supplier\Model\ResourceModel\Order\Reception\Item;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Supplier\Model\Order\Reception\Item', 'BoostMyShop\Supplier\Model\ResourceModel\Order\Reception\Item');
    }

    public function addReceptionFilter($receptionId)
    {
        $this->getSelect()->where("pori_por_id = ".$receptionId);

        return $this;
    }

    public function addOrderProductDetails($orderId)
    {
        $this->getSelect()->join($this->getTable('bms_purchase_order_product'), 'pori_product_id = pop_product_id and pop_po_id='.$orderId);
        return $this;
    }

}
