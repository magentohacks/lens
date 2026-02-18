<?php


namespace BoostMyShop\OrderPreparation\Controller\Adminhtml\Packing;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \BoostMyShop\OrderPreparation\Controller\Adminhtml\Packing
{

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @return ResponseInterface|void
     */
    public function execute()
    {
        $this->_initAction();

        $document = $this->getRequest()->getParam('document');

        $documentContent = null;
        $documentMimeType = null;
        $documentFileName = null;
        $date = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->date('Y-m-d_H-i-s');

        switch($document)
        {
            case 'shipping_label':
                $orderInProgress = $this->_coreRegistry->registry('current_packing_order');
                $template = $this->_carrierTemplateHelper->getCarrierTemplateForOrder($orderInProgress);
                $documentContent = $template->getShippingLabelFile([$orderInProgress]);
                $documentMimeType = $template->getct_export_file_mime();
                $documentFileName = $template->getct_export_file_name();
                break;
            case 'picking':
                $orderInProgress = $this->_coreRegistry->registry('current_packing_order');
                $obj = $this->_objectManager->create('BoostMyShop\OrderPreparation\Model\Pdf\PickingList');
                $obj->displaySummary(false);
                $documentContent = $obj->getPdf([$orderInProgress])->render();
                $documentMimeType = 'application/pdf';
                $documentFileName = 'picking_'.$orderInProgress->getOrder()->getincrement_id().'.pdf';
                break;
        }

        return $this->_objectManager->get('\Magento\Framework\App\Response\Http\FileFactory')->create(
            $documentFileName,
            $documentContent,
            DirectoryList::VAR_DIR,
            $documentMimeType
        );



    }
}
