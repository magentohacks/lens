<?php

namespace BoostMyShop\Rma\Model\ResourceModel\Rma;


class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('bms_rma_item', 'ri_id');
    }

}
