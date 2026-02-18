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
 * class Creditmemo
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Creditmemo extends AbstractAction
{
    /**
     * @var string
     */
    protected $_printType = 'creditmemo';

    /**
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getRenderingEntity()
    {
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');

        return $this->_objectManager->create('Magento\Sales\Model\Order\Creditmemo')->load($creditmemoId);
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\AbstractPdfTemplateRender
     */
    public function getPdfRenderer()
    {
        return $this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_CREDITMEMO);
    }
}