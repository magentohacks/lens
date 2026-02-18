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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml;

/**
 * abstract class AbstractActionRenderPdf
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractActionRenderPdf extends AbstractAction
{
    /**
     * @var \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager
     */
    protected $_pdfTemplateRenderManager;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\MPdfPrinterFactory
     */
    protected $_mPdfPrinterFactory;

    /**
     * AbstractActionRenderPdf constructor.
     *
     * @param Context                                                  $context
     * @param \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager $pdfTemplateRenderManager
     * @param \Magestore\Pdfinvoiceplus\Model\MPdfPrinterFactory       $mPdfPrinterFactory
     */
    public function __construct(
        \Magestore\Pdfinvoiceplus\Controller\Adminhtml\Context $context,
        \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager $pdfTemplateRenderManager,
        \Magestore\Pdfinvoiceplus\Model\MPdfPrinterFactory $mPdfPrinterFactory
    ) {
        parent::__construct($context);
        $this->_pdfTemplateRenderManager = $pdfTemplateRenderManager;
        $this->_mPdfPrinterFactory = $mPdfPrinterFactory;
    }

    /**
     * Check the permission to run it.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Pdfinvoiceplus::managetemplate');
    }
}