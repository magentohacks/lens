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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\PreviewDesign;

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
     * @return mixed
     */
    public function getRenderingEntity()
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection $collection */
        $collection = $this->_objectManager->create('Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection');

        return $collection->setPageSize(1)->setCurPage(1)->getFirstItem();
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface
     */
    public function getPdfRenderer()
    {
        return $this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_CREDITMEMO);
    }
}