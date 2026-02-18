<?php

namespace BoostMyShop\AdvancedStock\Model\ResourceModel\Routing;


class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {

    }

    /**
     * Calculate total sellable quantity for one product from warehouses
     *
     * @param $warehouses
     * @param $productId
     */
    public function calculateSellableQty($warehouses, $productId)
    {

        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('bms_advancedstock_warehouse_item'), array(new \Zend_Db_Expr('SUM(wi_available_quantity) as total')))
            ->where('wi_product_id = ' .$productId);
        if (count($warehouses) > 0)
            $select->where('wi_warehouse_id in (' .implode(',', $warehouses).')');
        $result = $this->getConnection()->fetchOne($select);
        return $result;
    }

}
