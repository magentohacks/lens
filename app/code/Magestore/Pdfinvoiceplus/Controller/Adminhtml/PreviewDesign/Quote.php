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
use Magento\Framework\Controller\ResultFactory;

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
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if (!$this->getPdfTemplate()->getId()) {
            $this->messageManager->addError(__('Can\' preview PDF template !'));
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }

        $designType = $this->getRequest()->getParam('design_type');

        $additionHtml = '<style>body{position: relative; border: 1px solid #c9c9c9;}</style>';
        $additionHtml .= '<style>div, p, span, th, td {word-wrap: break-word;word-break: break-all;}</style>';

        $html = $this->getPdfRenderer()->renderQuote(
            $this->getRenderingEntity(),
            $this->getPdfTemplate()->getData($designType . '_html')
        );

        $preg = "/(\s)contenteditable(\s*)=(\s*)[\"']?true[\"']?/i";
        $html = preg_replace($preg, " ", $html);
        $html = $additionHtml . $html;


        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setHeader('Content-type', 'text/html');
        $resultRaw->setContents($html);

        return $resultRaw;
    }

    /**
     * @return mixed
     */
    public function getRenderingEntity()
    {
        /** @var \Magento\Quote\Model\ResourceModel\Quote\Collection $collection */
        $collection = $this->_objectManager->create('Magento\Quote\Model\ResourceModel\Quote\Collection');
        // die($collection->setPageSize(1)->setCurPage(1)->getFirstItem()->getData('entity_id'));

        return $collection->setPageSize(1)->setCurPage(1)->getFirstItem();
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\Quote
     */
    public function getPdfRenderer()
    {
        return $this->_pdfTemplateRenderManager->create(PdfTemplateRenderManager::PDF_RENDER_QUOTE);
    }
}