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

namespace Magestore\Pdfinvoiceplus\Controller\PrintPdf;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * abstract class AbstractPrintPdf
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractPrintPdf extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magestore\Pdfinvoiceplus\Helper\Data
     */
    protected $_pdfInvoiceHelper;

    /**
     * AbstractPrintPdf constructor.
     *
     * @param \Magestore\Pdfinvoiceplus\Controller\Context $context
     */
    public function __construct(
        \Magestore\Pdfinvoiceplus\Controller\Context $context
    ) {
        parent::__construct($context);
        $this->_fileFactory = $context->getFileFactory();
        $this->_pdfInvoiceHelper = $context->getPdfHelper();
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $renderingEntity = $this->getRenderingEntity();
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $pdfTemplate */
        $pdfTemplate = $this->_pdfInvoiceHelper->getCurrentPdfTemplate($renderingEntity->getStoreId());
        try {
            /** @var \Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter $printAdapter */
            $printAdapter = $this->_objectManager->get('Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter');
            $printData = $printAdapter->printEntity($renderingEntity, $pdfTemplate);

            return $this->_fileFactory->create(
                $printData->getData('filename'),
                $printData->getData('content'),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());

            return $this->resultRedirectFactory->create()->setPath('*/*');
        }
    }

    /**
     * @return mixed
     */
    abstract public function getRenderingEntity();
}