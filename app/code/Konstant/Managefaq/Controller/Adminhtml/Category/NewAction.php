<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Category;

/**
 * New Category action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class NewAction extends \Konstant\Managefaq\Controller\Adminhtml\Category
{
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
