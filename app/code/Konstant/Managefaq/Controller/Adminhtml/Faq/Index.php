<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Faq;

/**
 * Index action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Index extends \Konstant\Managefaq\Controller\Adminhtml\Faq
{
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->_resultForwardFactory->create();
            $resultForward->forward('grid');

            return $resultForward;
        }

        $resultPage = $this->_resultPageFactory->create();

        $this->_addBreadcrumb(__('FAQ'), __('FAQ'));
        $this->_addBreadcrumb(__('Manage FAQ'), __('Manage FAQ'));

        return $resultPage;
    }
}
