<?php

namespace BoostMyShop\Rma\Model\ResourceModel\Rma\History;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Rma\Model\Rma\History', 'BoostMyShop\Rma\Model\ResourceModel\Rma\History');
    }

    public function addRmaFilter($rmaId)
    {
        $this->getSelect()->where('rh_rma_id = '.$rmaId);
        return $this;
    }

}
