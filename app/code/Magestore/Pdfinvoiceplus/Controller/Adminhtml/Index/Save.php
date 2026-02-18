<?php

/**
 * Magestore.
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

namespace Magestore\Pdfinvoiceplus\Controller\Adminhtml\Index;

/**
 * Save Tag Action.
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Save extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\AbstractAction
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            $id = $this->getRequest()->getParam(static::PARAM_CRUD_ID);

            /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
            $model = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\PdfTemplate')->load($id);
            $model->addData($data);

            if (is_array($model->getData('stores'))) {
                $model->setData('stores', implode(',', $model->getData('stores')));
            }

            try {
                $this->_imageHelper->mediaUploadImage(
                    $model,
                    'company_logo',
                    \Magestore\Pdfinvoiceplus\Model\PdfTemplate::IMAGE_LOGO_PATH
                );

                $model->save();

                $this->messageManager->addSuccess(__('The PDF Template has been saved.'));
                $this->_getSession()->setFormData(false);

                return $this->_getBackResultRedirect($resultRedirect, $model->getId());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addError(__('Something went wrong while saving the PDF Template.'));
                $this->_getSession()->setFormData($data);

                return $resultRedirect->setPath('*/*/edit', [static::PARAM_CRUD_ID => $id]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
