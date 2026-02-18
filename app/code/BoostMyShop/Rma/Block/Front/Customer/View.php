<?php

namespace BoostMyShop\Rma\Block\Front\Customer;

use Magento\Framework\Registry;

class View extends \Magento\Framework\View\Element\Template
{

    protected $_template = 'Rma/View.phtml';

    protected $_rmaCollectionFactory;
    protected $_customerSession;
    protected $_rmas;
    protected $registry;
    protected $_config;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \BoostMyShop\Rma\Model\Config $config,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->_customerSession = $customerSession;
        $this->_config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }



    public function getRma()
    {
        return $this->registry->registry('current_rma');
    }

    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', ['rma_id' => $this->getRma()->getId()]);
    }

    public function getReasonLabel($reason)
    {
        return $this->_config->getReasonLabel($reason);
    }

    public function getRequestLabel($request)
    {
        return $this->_config->getRequestLabel($request);
    }

    public function getPostMessageUrl()
    {
        return $this->getUrl('*/*/postMessage');
    }
}
