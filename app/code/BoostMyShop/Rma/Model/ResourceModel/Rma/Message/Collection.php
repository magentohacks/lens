<?php

namespace BoostMyShop\Rma\Model\ResourceModel\Rma\Message;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Rma\Model\Rma\Message', 'BoostMyShop\Rma\Model\ResourceModel\Rma\Message');
    }

    public function addRmaFilter($rmaId)
    {
        $this->getSelect()->where('rmm_rma_id = '.$rmaId);
        return $this;
    }

}
