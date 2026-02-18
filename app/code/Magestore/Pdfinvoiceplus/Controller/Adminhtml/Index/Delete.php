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
 * Delete Tag Action.
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Delete extends \Magestore\Pdfinvoiceplus\Controller\Adminhtml\AbstractAction
{
    /**
     * Delete action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam(self::PARAM_CRUD_ID);
        if ($id) {
            try {
                /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
                $model = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Model\PdfTemplate');
                $model->setId($id)->delete();
                $this->messageManager->addSuccess(__('You deleted the PDF Template.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [self::PARAM_CRUD_ID => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a PDF Template to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
