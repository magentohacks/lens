<?php

namespace BoostMyShop\Rma\Block\Front\Customer;

class SelectOrder extends \Magento\Framework\View\Element\Template
{

    protected $_template = 'Rma/SelectOrder.phtml';

    protected $_customerSession;
    protected $_config;
    protected $_orderCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \BoostMyShop\Rma\Model\Config $config,
        \Magento\Customer\Model\Session $customerSession,
        \BoostMyShop\Rma\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_config = $config;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Select order to return'));
    }

    public function getOrders()
    {
        $customerId = $this->_customerSession->getCustomerId();
        $collection = $this->_orderCollectionFactory->create()->addCustomerFilter($customerId);
        $collection->addStatusFilter($this->_config->getAllowedStatusesForReturnRequest());
        return $collection;
    }

    public function getSelectUrl($order)
    {
        return $this->getUrl('*/*/newRma', ['order_id' => $order->getId()]);
    }

}
