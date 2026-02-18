<?php

namespace BoostMyShop\OrderPreparation\Model\CarrierTemplate;

class Type implements \Magento\Framework\Option\ArrayInterface
{


    /**
     * Return array of carriers.
     * If $isActiveOnlyFlag is set to true, will return only active carriers
     *
     * @param bool $isActiveOnlyFlag
     * @return array
     */
    public function toOptionArray()
    {
        $methods = [];

        $methods['order_details_export'] = 'Order details file export';
        //$methods['simple_address_label'] = 'Simple address label';

        return $methods;
    }
}
