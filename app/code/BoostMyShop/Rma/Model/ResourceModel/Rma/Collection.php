<?php

namespace BoostMyShop\Rma\Model\ResourceModel\Rma;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Rma\Model\Rma', 'BoostMyShop\Rma\Model\ResourceModel\Rma');
    }

    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('rma_customer_id = '.$customerId);
        return $this;
    }

    public function addOrderFilter($orderId)
    {
        $this->getSelect()->where('rma_order_id = '.$orderId);
        return $this;
    }

    public function joinOrder()
    {
        $this->getSelect()->joinLeft(
            array('so' => $this->getResource()->getTable('sales_order')),
            'rma_order_id = so.entity_id',
            array('increment_id' => 'increment_id')
        );
        return $this;
    }

    public function addCustomerVisibleStatusesFilter()
    {
        $statuses = [];
        $statuses[] = "'".\BoostMyShop\Rma\Model\Rma\Status::requested."'";
        $statuses[] = "'".\BoostMyShop\Rma\Model\Rma\Status::accepted."'";
        $statuses[] = "'".\BoostMyShop\Rma\Model\Rma\Status::complete."'";

        $this->getSelect()->where('rma_status in ('.implode(',', $statuses).')');
        return $this;
    }

}
