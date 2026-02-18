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

use Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager;

/**
 * class Quote
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Quote extends AbstractAction
{
    /**
     * @var string
     */
    protected $_printType = 'quote';

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getRenderingEntity()
    {
        $quoteId = $this->getRequest()->getParam('quote_id');

        return $this->_objectManager->create('Magento\Quote\Model\Quote')->load($quoteId);
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface
     */
    public function getPdfRenderer()
    {
        return $this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_QUOTE);
    }

}