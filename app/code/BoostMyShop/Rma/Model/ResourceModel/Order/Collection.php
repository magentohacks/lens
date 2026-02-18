<?php

namespace BoostMyShop\Rma\Model\ResourceModel\Order;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Collection
{

    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('customer_id = '.$customerId);
        return $this;
    }

    public function addStatusFilter($statuses)
    {
        if (is_array($statuses) && (count($statuses) > 0)) {
            $list = [];
            foreach ($statuses as $status)
                $list[] = "'" . $status . "'";
            $this->getSelect()->where('status in (' . implode(',', $list) . ')');
        }
        return $this;
    }

}