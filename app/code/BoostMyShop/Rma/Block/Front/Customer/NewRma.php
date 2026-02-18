<?php

namespace BoostMyShop\Rma\Block\Front\Customer;

class NewRma extends \Magento\Framework\View\Element\Template
{

    protected $_template = 'Rma/NewRma.phtml';

    protected $_rmaCollectionFactory;
    protected $_customerSession;
    protected $_rmas;
    protected $_config;
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \BoostMyShop\Rma\Model\ResourceModel\Rma\CollectionFactory $rmaCollectionFactory,
        \BoostMyShop\Rma\Model\Config $config,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_rmaCollectionFactory = $rmaCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_config = $config;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Returns'));
    }

    public function getOrder()
    {
        return $this->_coreRegistry->registry('rma_order');
    }

    public function getReasons()
    {
        return $this->_config->getReasons();
    }

    public function getRequests()
    {
        return $this->_config->getRequests();
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/submitRequest');
    }
}
