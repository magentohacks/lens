<?php

namespace Konstant\Managefaq\Controller\Adminhtml;

/**
 * FAQ Abstract Action
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

abstract class Faq extends \Konstant\Managefaq\Controller\Adminhtml\AbstractAction
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Konstant_Managefaq::managefaq_faq');
    }
}
