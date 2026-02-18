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

namespace Magestore\Pdfinvoiceplus\Helper;

use Magestore\Pdfinvoiceplus\Model\PdfTemplate\Option\Statuses;

/**
 * class Data
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\ResourceModel\PdfTemplate\CollectionFactory
     */
    protected $_pdfTemplateCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * Block constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig,
        \Magestore\Pdfinvoiceplus\Model\ResourceModel\PdfTemplate\CollectionFactory $pdfTemplateCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_systemConfig = $systemConfig;
        $this->_pdfTemplateCollectionFactory = $pdfTemplateCollectionFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param string $templateText
     * @param array $variables
     *
     * @return mixed
     */
    public function mappingVariablesTemplate($templateText = '', array $variables = [])
    {
        if (empty($variables)) {
            return '';
        }

        /** @var \Magento\Email\Model\Template $pdfProcessTemplate */
        $pdfProcessTemplate = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\Email\Template');

        return $pdfProcessTemplate->setTemplateText($templateText)
            ->getProcessedTemplate($variables);
    }

    public function canshowCustomPrint($storeId = null)
    {
        return $this->_systemConfig->isEnablePdfInvoicePlus() && $this->getCurrentPdfTemplate($storeId)->getId();
    }

    /**
     * Get url for custom printing order
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return string
     */
    public function getCustomPdfPrintOrderUrl(\Magento\Sales\Model\Order $order)
    {
        return $this->_getUrl('pdfinvoiceplus/printPdf/order', ['order_id' => $order->getId()]);
    }

    /**
     * Get url for printing custom order
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     *
     * @return string
     */
    public function getCustomPdfPrintInvoiceUrl(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        return $this->_getUrl('pdfinvoiceplus/printPdf/invoice', ['invoice_id' => $invoice->getId()]);
    }

    /**
     * Get url for printing custom order
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     *
     * @return string
     */
    public function getCustomPdfPrintCreditmemoUrl(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        return $this->_getUrl('pdfinvoiceplus/printPdf/creditmemo', ['creditmemo_id' => $creditmemo->getId()]);
    }

    /**
     * Get url for printing custom shipment
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     *
     * @return string
     */
    public function getCustomPdfPrintShipmentUrl(\Magento\Sales\Model\Order\Shipment $shipment)
    {
        return $this->_getUrl('pdfinvoiceplus/printPdf/shipment', ['shipment_id' => $shipment->getId()]);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return string
     */
    public function getPrintAllInvoicesUrl(\Magento\Sales\Model\Order $order)
    {
        return $this->_getUrl('pdfinvoiceplus/printPdf/printAllInvoice', ['order_id' => $order->getId()]);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return string
     */
    public function getPrintAllCreditmemo(\Magento\Sales\Model\Order $order)
    {
        return $this->_getUrl('pdfinvoiceplus/printPdf/printAllCreditmemo', ['order_id' => $order->getId()]);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return string
     */
    public function getPrintAllShipment(\Magento\Sales\Model\Order $order)
    {
        return $this->_getUrl('pdfinvoiceplus/printPdf/printAllShipment', ['order_id' => $order->getId()]);
    }

    /**
     * @return bool
     */
    public function isDisableCorePrinting()
    {
        return $this->canshowCustomPrint() && $this->_systemConfig->isDisableCorePrinting();
    }

    /**
     * @return bool
     */
    public function canShowCustomPrintPdf()
    {
        return $this->_systemConfig->isEnablePdfInvoicePlus() && $this->getCurrentPdfTemplate()->getId();
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplate
     */
    public function getCurrentPdfTemplate($storeId = null)
    {
        /** @var \Magestore\Pdfinvoiceplus\Model\ResourceModel\PdfTemplate\Collection $pdfTemplateCollection */
        $pdfTemplateCollection = $this->_pdfTemplateCollectionFactory->create();
        $pdfTemplateCollection
            ->addFieldToFilter('status', ['eq' => Statuses::STATUS_ACTIVE]);

        if ($this->_systemConfig->isUseForMultiStore() && $storeId !== null) {
            $pdfTemplateCollection->addFieldToFilter('stores', ['finset' => $storeId]);
            if (!$pdfTemplateCollection->getSize()) {
                /** @var \Magestore\Pdfinvoiceplus\Model\ResourceModel\PdfTemplate\Collection $pdfTemplateCollection */
                $pdfTemplateCollection = $this->_pdfTemplateCollectionFactory->create();
                $pdfTemplateCollection
                    ->addFieldToFilter('status', ['eq' => Statuses::STATUS_ACTIVE])
                    ->addFieldToFilter('stores', ['finset' => 0]);
            }
        }

        $pdfTemplateCollection->setOrder('created_at', 'ASC');

        return $pdfTemplateCollection->setPageSize(1)->setCurPage($pdfTemplateCollection->getLastPageNumber())->getFirstItem();
    }

    /**
     * @return string
     */
    public function getStoreId(){
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return bool
     */
    public function getUseForMultiStore(){
        return $this->_systemConfig->isUseForMultiStore();
    }
}
