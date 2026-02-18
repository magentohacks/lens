<?php

namespace BoostMyShop\AdvancedStock\Model;


class Warehouse extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\AdvancedStock\Model\ResourceModel\Warehouse');
    }

    public function getFullAddress($html = false)
    {
        $address = [];
        $address[] = $this->getw_company_name();
        $address[] = $this->getw_street1();
        if ($this->getw_street2())
            $address[] = $this->getw_street2();
        $address[] = $this->getw_city().', '.$this->getw_postcode();
        $address[] = $this->getw_state();
        $address[] = $this->getw_country();
        if ($this->getw_telephone())
            $address[] = $this->getw_telephone();

        return implode(($html ? '<br>' : "\n"), $address);
    }

    public function applyDefaultValues()
    {
        $this->setw_is_active(1);
        return $this;
    }


    public function getAddress($html = false)
    {
        $separator = ($html ? '<br>' : "\n");

        $address = [];
        $address[] = $this->getw_company_name();
        $address[] = $this->getData('w_street1');
        $address[] = $this->getwData('w_street2');
        $address[] = $this->getw_postcode().' '.$this->getw_city();
        $address[] = $this->getw_state();
        $address[] = $this->getw_country();
        $address[] = $this->getw_telephone();

        return implode($separator, $address);
    }

}
