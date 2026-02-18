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

namespace Magestore\Pdfinvoiceplus\Model;

use Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager;

/**
 * class MPdfPrintAdapter
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class MPdfPrintAdapter
{
    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Data
     */
    protected $_pdfInvoiceHelper;

    /**
     * @var PdfTemplateRenderManager
     */
    protected $_pdfTemplateRenderManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var MPdfPrinterFactory
     */
    protected $_mPdfPrinterFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * stdlib timezone.
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_stdTimezone;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * MPdfPrintAdapter constructor.
     *
     * @param \Magestore\Pdfinvoiceplus\Helper\Data $pdfInvoiceHelper
     * @param \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager $pdfTemplateRenderManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param MPdfPrinterFactory $mPdfPrinterFactory
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $stdTimezone
     * @param SystemConfig $systemConfig
     */
    public function __construct(
        \Magestore\Pdfinvoiceplus\Helper\Data $pdfInvoiceHelper,
        PdfTemplateRenderManager $pdfTemplateRenderManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magestore\Pdfinvoiceplus\Model\MPdfPrinterFactory $mPdfPrinterFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone $stdTimezone,
        \Magestore\Pdfinvoiceplus\Model\SystemConfig $systemConfig
    )
    {
        $this->_pdfInvoiceHelper = $pdfInvoiceHelper;
        $this->_pdfTemplateRenderManager = $pdfTemplateRenderManager;
        $this->_objectManager = $objectManager;
        $this->_mPdfPrinterFactory = $mPdfPrinterFactory;
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_stdTimezone = $stdTimezone;
        $this->_systemConfig = $systemConfig;
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection $renderingCollection
     *
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function printRenderingCollection(
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection $renderingCollection
    ) {
        /** @var \Magento\Framework\DataObject $printData */
        $printData = $this->_dataObjectFactory->create();

        /** @var \Magento\Framework\DataObject $rendererData */
        $rendererData = $this->getPdfRendererData($renderingCollection);
        $printType = $rendererData->getData('print_type');
        $pdfRenderer = $rendererData->getData('renderer');

        if(!$pdfRenderer) {
            return $printData;
        }

        $date = $this->_stdTimezone->date()->format('Y-m-d_H-i-s');
        $filename = ucfirst($printType) . $date . '.pdf';

        $pageNum = 1;
        $lastPage = $renderingCollection->count();

        /** @var \Magestore\Pdfinvoiceplus\Model\MPdfPrinter $mpdfPrinter */
        $mpdfPrinter = $this->_mPdfPrinterFactory->create([
            'data' => [
                'enable_page_numbering' => $this->_systemConfig->allowPageNumber(),
            ]
        ]);

        foreach ($renderingCollection as $renderingEntity) {
            /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $pdfTemplate */
            $pdfTemplate = $this->_pdfInvoiceHelper->getCurrentPdfTemplate($renderingEntity->getStoreId());

            if (!$pdfTemplate->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __(sprintf(
                        'Not found PDF template for %s id %s!',
                        ucfirst($printType),
                        $renderingEntity->getId()
                    ))
                );
            }

            $html = $pdfRenderer->render(
                $renderingEntity,
                $pdfTemplate->getData($printType . '_html')
            );

            $pagebreak = ($pageNum == $lastPage) ? '' : '<pagebreak >';
            $mpdfPrinter->writeHtml($html . $pagebreak);
            $pageNum++;
        }

        return $printData->setData([
            'filename' => $filename,
            'content' => $mpdfPrinter->outputPdf()
        ]);
    }

    /**
     * @param \Magento\Sales\Model\AbstractModel $entity
     * @param PdfTemplate $pdfTemplate
     *
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\InputException
     */
    public function printEntity(
        \Magento\Sales\Model\AbstractModel $entity,
        \Magestore\Pdfinvoiceplus\Model\PdfTemplate $pdfTemplate
    ) {
        /** @var \Magento\Framework\DataObject $printData */
        $printData = $this->_dataObjectFactory->create();

        /** @var \Magento\Framework\DataObject $rendererData */
        $rendererData = $this->getPdfRendererData($entity);
        $printType = $rendererData->getData('print_type');
        $pdfRenderer = $rendererData->getData('renderer');

        if(!$pdfRenderer) {
            return $printData;
        }

        $html = $pdfRenderer->render(
            $entity,
            $pdfTemplate->getData($printType . '_html')
        );

        $filename = $this->_pdfInvoiceHelper->mappingVariablesTemplate(
                $pdfTemplate->getData($printType . '_filename'),
                $pdfRenderer->getVariables()
            ) . '.pdf';

        /** @var \Magestore\Pdfinvoiceplus\Model\MPdfPrinter $mpdfPrinter */
        $mpdfPrinter = $this->_mPdfPrinterFactory->create([
            'data' => [
                'enable_page_numbering' => $this->_systemConfig->allowPageNumber(),
                'filename' => $filename,
                'orientation' => $pdfTemplate->getData('orientation'),
                'format' => $pdfTemplate->getData('format')
            ]
        ]);

        return $printData->setData([
            'filename' => $filename,
            'content' => $mpdfPrinter->printPdf($html)
        ]);
    }

    /**
     * @param \Magento\Quote\Model\Quote $entity
     * @param PdfTemplate $pdfTemplate
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\InputException
     */
    public function printQuote(
        \Magento\Quote\Model\Quote $entity,
        \Magestore\Pdfinvoiceplus\Model\PdfTemplate $pdfTemplate
    )
    {
        /** @var \Magento\Framework\DataObject $printData */
        $printData = $this->_dataObjectFactory->create();

        /** @var \Magento\Framework\DataObject $rendererData */
        $rendererData = $this->getPdfRendererData($entity);
        $printType = $rendererData->getData('print_type');
        $pdfRenderer = $rendererData->getData('renderer');

        if(!$pdfRenderer) {
            return $printData;
        }

        $html = $pdfRenderer->renderQuote(
            $entity,
            $pdfTemplate->getData($printType . '_html')
        );

        $filename = $this->_pdfInvoiceHelper->mappingVariablesTemplate(
                $pdfTemplate->getData($printType . '_filename'),
                $pdfRenderer->getVariables()
            ) . '.pdf';

        /** @var \Magestore\Pdfinvoiceplus\Model\MPdfPrinter $mpdfPrinter */
        $mpdfPrinter = $this->_mPdfPrinterFactory->create([
            'data' => [
                'enable_page_numbering' => $this->_systemConfig->allowPageNumber(),
                'filename' => $filename,
                'orientation' => $pdfTemplate->getData('orientation'),
                'format' => $pdfTemplate->getData('format')
            ]
        ]);

        return $printData->setData([
            'filename' => $filename,
            'content' => $mpdfPrinter->printPdf($html)
        ]);
    }

    /**
     * @param mixed
     *
     * @return PdfTemplateRenderInterface
     */
    public function getPdfRendererData($object)
    {
        if($object instanceof \Magento\Sales\Api\Data\OrderInterface
            || $object instanceof \Magento\Sales\Model\ResourceModel\Order\Collection) {

            return $this->_dataObjectFactory->create([
                'data' => [
                    'renderer' => $this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_ORDER),
                    'print_type' => 'order'
                ]
            ]);
        }

        if ($object instanceof \Magento\Sales\Api\Data\InvoiceInterface
            || $object instanceof \Magento\Sales\Model\ResourceModel\Order\Invoice\Collection) {

            return $this->_dataObjectFactory->create([
                'data' => [
                    'renderer' => $this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_INVOICE),
                    'print_type' => 'invoice'
                ]
            ]);
        }

        if ($object instanceof \Magento\Sales\Api\Data\CreditmemoInterface
            || $object instanceof \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection) {

            return $this->_dataObjectFactory->create([
                'data' => [
                    'renderer' => $this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_CREDITMEMO),
                    'print_type' => 'creditmemo'
                ]
            ]);
        }

        if ($object instanceof \Magento\Sales\Api\Data\ShipmentInterface
            || $object instanceof \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection) {

            return $this->_dataObjectFactory->create([
                'data' => [
                    'renderer' => $this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_SHIPMENT),
                    'print_type' => 'shipment'
                ]
            ]);
        }

        if ($object instanceof \Magento\Quote\Model\Quote) {

            return $this->_dataObjectFactory->create([
                'data' => [
                    'renderer' => $this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_QUOTE),
                    'print_type' => 'quote'
                ]
            ]);
        }

        return $this->_dataObjectFactory->create();
    }
}