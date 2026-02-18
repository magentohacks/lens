<?php

namespace Konstant\Managefaq\Controller\Adminhtml\Faq;

/**
 * Grid action.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Grid extends \Konstant\Managefaq\Controller\Adminhtml\Faq
{
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
		
        return $resultLayout;
    }
}
