<?php

namespace BoostMyShop\Rma\Model\ResourceModel\Rma;


class Message extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('bms_rma_messages', 'rmm_id');
    }

}
