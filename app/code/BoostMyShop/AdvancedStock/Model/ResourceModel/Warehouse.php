<?php

namespace BoostMyShop\AdvancedStock\Model\ResourceModel;


class Warehouse extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('bms_advancedstock_warehouse', 'w_id');
    }


}
