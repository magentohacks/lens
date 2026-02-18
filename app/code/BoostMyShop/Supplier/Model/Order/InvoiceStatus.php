<?php

namespace BoostMyShop\Supplier\Model\Order;

class InvoiceStatus implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = array();

        $options['missing'] = __('Missing');
        $options['to_pay'] = __('To pay');
        $options['paid'] = __('Paid');

        return $options;
    }

}
