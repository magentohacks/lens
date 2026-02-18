<?php

namespace BoostMyShop\OrderPreparation\Controller\Adminhtml;

abstract class Preparation extends \Magento\Backend\App\AbstractAction
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    protected $_resultLayoutFactory;
    protected $_layoutFactory;
    protected $_backendAuthSession;
    protected $_orderPreparationFactory;
    protected $_orderFactory;
    protected $_invoicePdf;
    protected $_shipmentPdf;
    protected $_invoiceCollectionFactory;
    protected $_shipmentCollectionFactory;
    protected $_configFactory = null;
    protected $_preparationRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\User\Model\UserFactory $userFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \BoostMyShop\OrderPreparation\Model\OrderPreparationFactory $orderPreparationFactory,
        \Magento\Sales\Model\Order\Pdf\Invoice $invoicePdf,
        \Magento\Sales\Model\Order\Pdf\Shipment $shipmentPdf,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \BoostMyShop\OrderPreparation\Model\ConfigFactory $configFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \BoostMyShop\OrderPreparation\Model\Registry $preparationRegistry
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_preparationRegistry = $preparationRegistry;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_layoutFactory = $layoutFactory;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_orderPreparationFactory = $orderPreparationFactory;
        $this->_orderFactory = $orderFactory;
        $this->_invoicePdf = $invoicePdf;
        $this->_shipmentPdf = $shipmentPdf;
        $this->_invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->_shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->_configFactory = $configFactory;

    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();

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
