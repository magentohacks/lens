<?php

namespace BoostMyShop\Rma\Model\Rma;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    const draft = 'draft';
    const requested = 'requested';
    const accepted = 'accepted';
    const processing = 'processing';
    const complete = 'complete';
    const canceled = 'canceled';

    public function getStatuses($appendEmpty = true)
    {
        $options = array();

        if ($appendEmpty)
            $options[] = array('value' => '', 'label' => '');

        $options[] = array('value' => 'draft', 'label' => __('Draft'));
        $options[] = array('value' => 'requested', 'label' => __('Requested'));
        $options[] = array('value' => 'accepted', 'label' => __('Accepted'));
        $options[] = array('value' => 'processing', 'label' => __('Processing'));
        $options[] = array('value' => 'complete', 'label' => __('Complete'));
        $options[] = array('value' => 'expired', 'label' => __('Expired '));
        $options[] = array('value' => 'canceled', 'label' => __('Canceled'));

        return $options;
    }

    public function toOptionArray()
    {
        $options = array();

        foreach($this->getStatuses(false) as $item)
        {
            $options[$item['value']] = $item['label'];
        }

        return $options;
    }

}
