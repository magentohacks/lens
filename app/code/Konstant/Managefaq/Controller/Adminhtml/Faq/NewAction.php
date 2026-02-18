<?php
namespace Konstant\Managefaq\Controller\Adminhtml\Faq;

/**
 * New FAQ action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class NewAction extends \Konstant\Managefaq\Controller\Adminhtml\Faq
{
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
