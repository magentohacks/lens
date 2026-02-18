<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Faq;

/**
 * Delete FAQ action
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Delete extends \Konstant\Managefaq\Controller\Adminhtml\Faq
{
    public function execute()
    {
        $Id = $this->getRequest()->getParam('id');
        try {
            $faq = $this->_faqFactory->create()->setId($Id);
            $faq->delete();
            $this->messageManager->addSuccess(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
