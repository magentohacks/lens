<?php

namespace BoostMyShop\Rma\Model\ResourceModel\Rma\Item;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Rma\Model\Rma\Item', 'BoostMyShop\Rma\Model\ResourceModel\Rma\Item');
    }

    public function addRmaFilter($rmaId)
    {
        $this->getSelect()->where('ri_rma_id = '.$rmaId);
        return $this;
    }

}
