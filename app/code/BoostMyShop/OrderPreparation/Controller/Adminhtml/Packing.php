<?php

namespace BoostMyShop\OrderPreparation\Controller\Adminhtml;

abstract class Packing extends \Magento\Backend\App\AbstractAction
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    protected $_resultLayoutFactory;
    protected $_backendAuthSession;
    protected $_orderPreparationFactory;
    protected $_orderFactory;
    protected $_inProgressFactory;
    protected $_configFactory = null;
    protected $_carrierTemplateHelper = null;
    protected $_logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\User\Model\UserFactory $userFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \BoostMyShop\OrderPreparation\Model\OrderPreparationFactory $orderPreparationFactory,
        \BoostMyShop\OrderPreparation\Model\InProgressFactory $inProgressFactory,
        \BoostMyShop\OrderPreparation\Model\ConfigFactory $configFactory,
        \BoostMyShop\OrderPreparation\Helper\CarrierTemplate $carrierTemplateHelper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \BoostMyShop\OrderPreparation\Helper\Logger $logger
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_configFactory = $configFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_orderPreparationFactory = $orderPreparationFactory;
        $this->_orderFactory = $orderFactory;
        $this->_inProgressFactory = $inProgressFactory;
        $this->_carrierTemplateHelper = $carrierTemplateHelper;
        $this->_logger = $logger;
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();

        $id = $this->getRequest()->getParam('order_id');
        if (!$id)
            $id = $this->getRequest()->getPost('order_id');
        $model = $this->_inProgressFactory->create()->load($id);
        $this->_coreRegistry->register('current_packing_order', $model);

        return $this;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
