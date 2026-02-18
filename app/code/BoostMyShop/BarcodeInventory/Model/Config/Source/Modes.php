<?php

namespace BoostMyShop\BarcodeInventory\Model\Config\Source;

class Modes implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray($appendEmpty = true)
    {
        $options = array();

        if ($appendEmpty)
            $options[] = array('value' => '', 'label' => __('--Please Select--'));

        $options[] = array('value' => 'decrease', 'label' => __('Decrease'));
        $options[] = array('value' => 'increase', 'label' => __('Increase'));
        $options[] = array('value' => 'manual', 'label' => __('Manual'));

        return $options;
    }

}
