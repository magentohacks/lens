<?php

namespace BoostMyShop\Supplier\Model\ResourceModel;


class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('bms_purchase_order', 'po_id');
    }

    public function getNextReference($type = 'PO')
    {
        if (!$type)
            $type = 'po';
        $prefix = $type.'-'.date('Ymd').'-';

        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->getMainTable(), array(new \Zend_Db_Expr('max(po_reference) as maxReference')))
            ->where('po_reference like "' .$prefix. '%" ');
        $result = $connection->fetchOne($select);

        if (!$result)
            $result = $prefix.'0001';
        else
            $result++;

        return strtoupper($result);
    }

    public function updateDeliveryProgress($orderId)
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->getTable('bms_purchase_order_product'), array(new \Zend_Db_Expr('SUM(pop_qty_received) / SUM(pop_qty) * 100 as delivery_progress')))
            ->where('pop_po_id = ' .$orderId);
        $result = $connection->fetchOne($select);

        $data['po_delivery_progress'] = $result;
        $condition = 'po_id = '.$orderId;
        $connection->update($this->getMainTable(), $data, $condition);

        return $this;
    }

    public function updateTotals($order)
    {
        $data = [];

        $data['po_subtotal'] = $this->getProductsSubTotal($order->getId(), false);
        $data['po_subtotal_base'] = $this->getProductsSubTotal($order->getId(), true);

        $data['po_tax'] = $this->getProductsTax($order->getId(), false) + ($order->getpo_shipping_cost() + $order->getpo_additionnal_cost()) / 100 * $order->getpo_tax_rate();
        $data['po_tax_base'] = $this->getProductsTax($order->getId(), true) + ($order->getpo_shipping_cost_base() + $order->getpo_additionnal_cost_base()) / 100 * $order->getpo_tax_rate();

        $data['po_grandtotal'] = $data['po_subtotal'] + $order->getpo_shipping_cost() + $order->getpo_additionnal_cost() + $data['po_tax'];
        $data['po_grandtotal_base'] = $data['po_subtotal_base'] + $order->getpo_shipping_cost_base() + $order->getpo_additionnal_cost_base() + $data['po_tax_base'];

        $condition = 'po_id = '.$order->getId();
        $this->getConnection()->update($this->getMainTable(), $data, $condition);

        return $this;
    }

    protected function getProductsSubTotal($orderId, $base = false)
    {
        $columnName = ($base ? 'pop_subtotal_base' : 'pop_subtotal');
        $select = $this->getConnection()
                        ->select()
                        ->from($this->getTable('bms_purchase_order_product'), array(new \Zend_Db_Expr('SUM('.$columnName.') as subtotal')))
                        ->where('pop_po_id = ' .$orderId);
        $result = $this->getConnection()->fetchOne($select);
        return $result;
    }

    protected function getProductsTax($orderId, $base = false)
    {
        $columnName = ($base ? 'pop_tax_base' : 'pop_tax');
        $select = $this->getConnection()
            ->select()
            ->from($this->getTable('bms_purchase_order_product'), array(new \Zend_Db_Expr('SUM('.$columnName.') as subtotal')))
            ->where('pop_po_id = ' .$orderId);
        $result = $this->getConnection()->fetchOne($select);
        return $result;
    }

}
