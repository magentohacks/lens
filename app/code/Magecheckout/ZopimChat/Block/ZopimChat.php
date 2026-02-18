<?php

namespace Magecheckout\ZopimChat\Block;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magecheckout\ZopimChat\Helper\Data as HelperData;
use Magento\Framework\ObjectManagerInterface;

class ZopimChat extends Template
{
    protected $helperData;
    protected $objectFactory;

    public function __construct(
        Context $context,
        HelperData $helperData,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->helperData    = $helperData;
        $this->objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function getHelper()
    {
        return $this->helperData;
    }
}