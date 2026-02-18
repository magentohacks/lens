<?php

namespace BoostMyShop\Supplier\Model\ResourceModel\Supplier;


class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('bms_supplier_product', 'sp_id');
    }

    public function getIdFromProductSupplier($productId, $supplierId)
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->getMainTable(),array('sp_id'))
            ->where('sp_product_id = ' .$productId.' and sp_sup_id = '.$supplierId);

        return $connection->fetchOne($select);

    }

    public function removeOtherPrimary($productId, $supplierId)
    {
        $this->getConnection()->update($this->getMainTable(), ['sp_primary' => 0], 'sp_product_id='.$productId.' and sp_sup_id <> '.$supplierId);
        return $this;
    }

}
