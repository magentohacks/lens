<?php

namespace BoostMyShop\Rma\Block\Front\Customer;

class Grid extends \Magento\Framework\View\Element\Template
{

    protected $_template = 'Rma/Grid.phtml';

    protected $_rmaCollectionFactory;
    protected $_customerSession;
    protected $_rmas;
    protected $_config;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \BoostMyShop\Rma\Model\ResourceModel\Rma\CollectionFactory $rmaCollectionFactory,
        \BoostMyShop\Rma\Model\Config $config,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_rmaCollectionFactory = $rmaCollectionFactory;
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
        $this->pageConfig->getTitle()->set(__('My Returns'));
    }



    public function getRmas()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->_rmas) {
            $this->_rmas = $this->_rmaCollectionFactory->create()->addCustomerFilter($this->_customerSession->getCustomerId())->addCustomerVisibleStatusesFilter();
        }
        return $this->_rmas;
    }

    public function getProductDetails($rma)
    {
        $html = [];

        foreach($rma->getAllItems() as $item)
        {
            $html[] = $item->getri_qty().'x '.$item->getri_name();
        }

        return implode('<br />', $html);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($rma)
    {
        return $this->getUrl('*/*/view', ['rma_id' => $rma->getId()]);
    }

    public function getNewReturnUrl()
    {
        return $this->getUrl('*/*/selectOrder');
    }

    public function canRequestReturn()
    {
        return $this->_config->canRequestReturn();
    }

}
