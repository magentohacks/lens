<?php

namespace BoostMyShop\Rma\Controller\Adminhtml\Rma;

class Index extends \BoostMyShop\Rma\Controller\Adminhtml\Rma
{

    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage returns'));
        $this->_view->renderLayout();
    }
}
