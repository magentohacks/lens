<?php
namespace Konstant\Managefaq\Controller\Adminhtml;

/**
 * Category Abstract Action
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

abstract class Category extends \Konstant\Managefaq\Controller\Adminhtml\AbstractAction
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Konstant_Managefaq::managefaq_category');
    }
}
