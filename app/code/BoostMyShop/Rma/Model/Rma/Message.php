<?php

namespace BoostMyShop\Rma\Model\Rma;


class Message extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Rma\Model\ResourceModel\Rma\Message');
    }


}