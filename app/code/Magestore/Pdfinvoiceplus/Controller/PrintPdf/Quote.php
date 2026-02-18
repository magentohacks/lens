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

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * class Quote
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Quote extends AbstractPrintPdf
{

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function execute()
    {
        $renderingEntity = $this->getRenderingEntity();
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $pdfTemplate */
        $pdfTemplate = $this->_pdfInvoiceHelper->getCurrentPdfTemplate($renderingEntity->getStoreId());
        try {
            /** @var \Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter $printAdapter */
            $printAdapter = $this->_objectManager->get('Magestore\Pdfinvoiceplus\Model\MPdfPrintAdapter');
            $printData = $printAdapter->printQuote($renderingEntity, $pdfTemplate);

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
     * @return \Magento\Quote\Model\Quote
     */
    public function getRenderingEntity()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Session')->getQuote();
    }
}