<?php

namespace BoostMyShop\Supplier\Model\ResourceModel\Order;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Supplier\Model\Order', 'BoostMyShop\Supplier\Model\ResourceModel\Order');
    }

    public function addSupplierFilter($supplierId)
    {

        $this->getSelect()->where("po_sup_id = ".$supplierId);

        return $this;
    }

}
