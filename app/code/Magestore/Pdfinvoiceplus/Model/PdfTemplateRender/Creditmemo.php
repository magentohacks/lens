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

namespace Magestore\Pdfinvoiceplus\Model\PdfTemplateRender;

use \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderManager;

/**
 * class Creditmemo
 *
 * @method \Magento\Sales\Model\Order|Creditmemo getRenderingEntity()
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Creditmemo extends AbstractRender
{
    protected $_type = 'creditmemo';

    /**
     * @return array
     */
    public function getVariables()
    {
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\VariableCollector $varCollector */
        $varCollector = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\VariableCollector');
        $templateVars = $varCollector->setData('order', $this->getOrder())
            ->setData('creditmemo', $this->getCreditmemo())
            ->setData('type', $this->getType())
            ->getInfoMergedVariables();

        return $this->processAllVars($templateVars);
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getRenderingEntity()->getOrder();
    }

    public function getCreditmemo()
    {
        return $this->getRenderingEntity();
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface
     */
    public function getTotalRenderer()
    {
        return $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\TotalRender\Creditmemo');
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface
     */
    public function getItemRenderer()
    {
        if (!$this->_itemRenderer) {
            $this->setItemRenderer($this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_INVOICE_ITEM));
        }

        return $this->_itemRenderer;
    }
}