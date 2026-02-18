<?php

namespace BoostMyShop\Rma\Controller\Adminhtml\Rma;

class SelectOrder extends \BoostMyShop\Rma\Controller\Adminhtml\Rma
{

    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Select order for new RMA'));
        $this->_view->renderLayout();
    }
}
