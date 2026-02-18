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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\Design\SyncInformation;

use Magento\Framework\App\ResponseInterface;
use Magestore\Pdfinvoiceplus\Model\PdfTemplate;

/**
 * class ResetTemplate
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class ResetTemplate extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\Design
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $designType = $this->getRequest()->getParam('design_type');
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
        $model = $this->_getPdfTemplateModel();
        if (!$model->getId()) {
            $this->messageManager->addError(__('This PDF Template no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        switch ($designType) {
            case PdfTemplate::DESIGN_TYPE_ORDER:
                $model->setAllowResetOrderTemplate(true);
                break;
            case PdfTemplate::DESIGN_TYPE_INVOICE:
                $model->setAllowResetInvoiceTemplate(true);
                break;
            case PdfTemplate::DESIGN_TYPE_CREDITMEMO:
                $model->setAllowResetCreditmemoTemplate(true);
                break;
        }

        try {
            $model->setIsChangeDesign(true)->save();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->messageManager->addError(__('Something went wrong while saving the PDF Tempalte.'));

            return $resultRedirect->setPath('*/*/edit', [static::PARAM_CRUD_ID => $model->getId()]);
        }

        if ($this->getRequest()->getParam('advance')) {
            return $resultRedirect->setPath(
                '*/design/advanceEdit',
                [
                    '_current'            => true,
                    static::PARAM_CRUD_ID => $model->getId(),
                    'design_type'         => $designType,
                ]
            );
        }

        return $resultRedirect->setPath(
            '*/design/edit',
            [
                '_current'            => true,
                static::PARAM_CRUD_ID => $model->getId(),
                'design_type'         => $designType,
            ]
        );
    }
}