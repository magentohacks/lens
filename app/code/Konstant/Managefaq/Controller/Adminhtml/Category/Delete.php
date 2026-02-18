<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Category;

/**
 * Delete Category action
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Delete extends \Konstant\Managefaq\Controller\Adminhtml\Category
{
    public function execute()
    {
        $categoryId = $this->getRequest()->getParam('id');
        try {
			$category = $this->_objectManager->create('Konstant\Managefaq\Model\Category')->setId($categoryId);
            $category->delete();
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
