<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Category;

/**
 * Category Index action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Index extends \Konstant\Managefaq\Controller\Adminhtml\Category
{
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->_resultForwardFactory->create();
            $resultForward->forward('grid');

            return $resultForward;
        }

        $resultPage = $this->_resultPageFactory->create();

        $this->_addBreadcrumb(__('Category'), __('Category'));
        $this->_addBreadcrumb(__('Manage Category'), __('Manage Category'));

        return $resultPage;
    }
}
