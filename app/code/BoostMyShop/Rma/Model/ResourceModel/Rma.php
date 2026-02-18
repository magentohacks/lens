<?php

namespace BoostMyShop\Rma\Model\ResourceModel;


class Rma extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('bms_rma', 'rma_id');
    }

}
