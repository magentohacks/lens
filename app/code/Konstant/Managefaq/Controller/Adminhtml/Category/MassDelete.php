<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Category;

/**
 * Category MassDelete action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class MassDelete extends \Konstant\Managefaq\Controller\Adminhtml\Category
{
    public function execute()
    {
        $categoryIds = $this->getRequest()->getParam('category');
        if (!is_array($categoryIds) || empty($categoryIds)) {
            $this->messageManager->addError(__('Please select category(s).'));
        } else {
            try {
				foreach($categoryIds as $categoryId) {
					$categoryCollection = $this->_objectManager->create('Konstant\Managefaq\Model\Category')->load($categoryId);
					$categoryCollection->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($categoryIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
