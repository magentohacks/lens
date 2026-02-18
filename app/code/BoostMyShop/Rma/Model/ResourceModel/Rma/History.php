<?php

namespace BoostMyShop\Rma\Model\ResourceModel\Rma;


class History extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('bms_rma_history', 'rh_id');
    }

}
