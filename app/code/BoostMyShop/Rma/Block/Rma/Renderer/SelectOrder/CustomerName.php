<?php

namespace BoostMyShop\Rma\Block\Rma\Renderer\SelectOrder;

use Magento\Framework\DataObject;

class CustomerName extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{


    public function render(DataObject $order)
    {
        return $order->getcustomer_firstname().' '.$order->getcustomer_lastname();
    }
}