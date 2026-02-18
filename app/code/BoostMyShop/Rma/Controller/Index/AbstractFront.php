<?php

namespace BoostMyShop\Rma\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

abstract class AbstractFront extends \Magento\Framework\App\Action\Action
{

    protected $registry;
    protected $_rmaFactory;
    protected $resultPageFactory;
    protected $httpContext;
    protected $_rmaFromOrder;
    protected $_customerSession;
    protected $_adminNotification;


    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \BoostMyShop\Rma\Model\RmaFactory $rmaFactory,
        \BoostMyShop\Rma\Model\Rma\AdminNotification $adminNotification,
        \BoostMyShop\Rma\Model\Rma\CreateFromOrder $rmaFromOrder
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->_rmaFactory = $rmaFactory;
        $this->registry = $registry;
        $this->_orderFactory = $orderFactory;
        $this->httpContext = $httpContext;
        $this->_rmaFromOrder = $rmaFromOrder;
        $this->_adminNotification = $adminNotification;
        $this->_customerSession = $customerSession;
    }

    public function mustRedirect()
    {
        if (!$this->isLoggedIn())
        {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
        else
            return false;
    }

    public function checkRmaAuthorization($rmaId)
    {
        $rma = $this->_rmaFactory->create()->load($rmaId);
        if ($rma->getrma_customer_id() != $this->_customerSession->getCustomerId())
        {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('rma/index/index');
            return $resultRedirect;
        }
    }

    public function checkOrderAuthorization($order)
    {
        if ($order->getcustomer_id() != $this->_customerSession->getCustomerId())
        {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('rma/index/index');
            return $resultRedirect;
        }
    }

    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

}
