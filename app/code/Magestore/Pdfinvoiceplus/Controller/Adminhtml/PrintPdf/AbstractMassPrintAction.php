<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\PrintPdf;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

/**
 * abstract class AbstractMassPrintAction
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractMassPrintAction extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\AbstractActionRenderPdf
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory
     */
    protected $_invoiceCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory
     */
    protected $_creditmemoCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $_shipmentCollectionFactory;

    /**
     * stdlib timezone.
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_stdTimezone;

    /**
     * AbstractMassPrintAction constructor.
     *
     * @param \Magestore\Pdfinvoiceplus\Controller\Adminhtml\Context $context
     * @param \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager $pdfTemplateRenderManager
     * @param \Magestore\Pdfinvoiceplus\Model\MPdfPrinterFactory $mPdfPrinterFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $creditmemoCollectionFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $stdTimezone
     */
    public function __construct(
        \Magestore\Pdfinvoiceplus\Controller\Adminhtml\Context $context,
        \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager $pdfTemplateRenderManager,
        \Magestore\Pdfinvoiceplus\Model\MPdfPrinterFactory $mPdfPrinterFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory $creditmemoCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\Stdlib\DateTime\Timezone $stdTimezone
    )
    {
        parent::__construct($context, $pdfTemplateRenderManager, $mPdfPrinterFactory);
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->_creditmemoCollectionFactory = $creditmemoCollectionFactory;
        $this->_shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->filter = $filter;
        $this->_stdTimezone = $stdTimezone;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        try {
            /** @var \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection $renderingCollection */
            $renderingCollection = $this->_prepareRenderingCollectionBeforePrint($this->getRenderingCollection());
            /** @var \Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter $printAdapter */
            $printAdapter = $this->_objectManager->get('Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter');
            $printData = $printAdapter->printRenderingCollection($renderingCollection);

            return $this->_fileFactory->create(
                $printData->getData('filename'),
                $printData->getData('content'),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addWarning($e->getMessage());

            return $this->_getFailResultPrintRedirect($this->resultRedirectFactory->create());
        }
    }


    /**
     * @param \Magento\Framework\Controller\Result\Redirect $resultRedirect
     */
    protected function _getFailResultPrintRedirect(
        \Magento\Framework\Controller\Result\Redirect $resultRedirect
    )
    {
        switch ($this->getRequest()->getParam('namespace')) {
            case 'sales_order_invoice_grid':
                $resultRedirect->setPath('sales/invoice');
                break;
            case 'sales_order_creditmemo_grid':
                $resultRedirect->setPath('sales/creditmemo');
                break;
            case 'sales_order_shipment_grid':
                $resultRedirect->setPath('sales/shipment');
                break;

            default:
                $resultRedirect->setPath('sales/order');
        }

        return $resultRedirect;
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection
     */
    abstract public function getRenderingCollection();

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection $collection
     *
     * @return mixed
     */
    abstract protected function _prepareRenderingCollectionBeforePrint(
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection $collection
    );
}