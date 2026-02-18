<?php

namespace BoostMyShop\Rma\Controller\Adminhtml\Rma;

class Edit extends \BoostMyShop\Rma\Controller\Adminhtml\Rma
{
    /**
     * @return void
     */
    public function execute()
    {

        $rmaId = $this->getRequest()->getParam('rma_id');
        $rma = $this->_rmaFactory->create()->load($rmaId);

        $this->_coreRegistry->register('current_rma', $rma);

        if (isset($rmaId)) {
            $breadcrumb = __('Edit RMA');
        } else {
            $breadcrumb = __('New RMA');
        }
        $this->_initAction()->_addBreadcrumb($breadcrumb, $breadcrumb);

        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Edit RMA '));
        $this->_view->getPage()->getConfig()->getTitle()->prepend($rma->getId() ? __("Edit RMA '%1'", $rma->getrma_reference()) : __('New RMA'));
        $this->_view->renderLayout();
    }
}
