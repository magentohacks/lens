<?php

namespace BoostMyShop\Supplier\Model\ResourceModel\Order\Product;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Supplier\Model\Order\Product', 'BoostMyShop\Supplier\Model\ResourceModel\Order\Product');
    }

    public function addOrderFilter($orderId)
    {

        $this->getSelect()->where("pop_po_id = ".$orderId);

        return $this;
    }

    public function addProductFilter($productId)
    {

        $this->getSelect()->where("pop_product_id = ".$productId);

        return $this;
    }

    public function getAlreadyAddedProductIds($orderId)
    {
        $this->getSelect()->reset()->from($this->getMainTable(), ['pop_product_id'])->where("pop_po_id = ".$orderId);
        $ids = $this->getConnection()->fetchCol($this->getSelect());

        return $ids;
    }

    public function getOrdersHistory($productId)
    {
        $this->getSelect()
            ->where("pop_product_id = ".$productId)
            ->join($this->getTable('bms_purchase_order'), 'pop_po_id = po_id')
            ->join($this->getTable('bms_supplier'), 'po_sup_id = sup_id');
        return $this;
    }

    public function addOrderStatusFilter($status)
    {
        $this->getSelect()
            ->join($this->getTable('bms_purchase_order'), 'pop_po_id = po_id')
            ->join($this->getTable('bms_supplier'), 'po_sup_id = sup_id')
            ->where("po_status = '".$status."'");

        return $this;
    }

    public function addExpectedFilter()
    {
        $this->getSelect()->where('pop_qty > pop_qty_received');
        return $this;
    }
}
