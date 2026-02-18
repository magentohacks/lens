<?php

namespace BoostMyShop\Supplier\Model\ResourceModel\Supplier\Product;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Supplier\Model\Supplier\Product', 'BoostMyShop\Supplier\Model\ResourceModel\Supplier\Product');
    }

    public function getSuppliers($productId)
    {
        $this->getSelect()->where("sp_product_id = ".$productId)->join($this->getTable('bms_supplier'), 'sup_id = sp_sup_id');

        return $this;
    }

    public function getProductIdsForSupplier($supplierId)
    {
        $this->getSelect()->reset()->from($this->getMainTable(), ['sp_product_id'])->where("sp_sup_id= ".$supplierId);
        return $this->getConnection()->fetchCol($this->getSelect());
    }
}
