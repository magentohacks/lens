<?php


namespace BoostMyShop\OrderPreparation\Controller\Adminhtml\Preparation;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class PickingList extends \BoostMyShop\OrderPreparation\Controller\Adminhtml\Preparation
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
        $userId = $this->_preparationRegistry->getCurrentOperatorId();
        $warehouseId = $this->_preparationRegistry->getCurrentWarehouseId();
        $orders = $this->_objectManager->get('\BoostMyShop\OrderPreparation\Model\ResourceModel\InProgress\CollectionFactory')
                            ->create()
                            ->addOrderDetails()
                            ->addWarehouseFilter($warehouseId)
                            ->addUserFilter($userId);

        if (count($orders) > 0)
        {
            $obj = $this->_objectManager->create('BoostMyShop\OrderPreparation\Model\Pdf\PickingList');
            $pdf = $obj->getPdf($orders);
            $date = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->date('Y-m-d_H-i-s');
            return $this->_objectManager->get('\Magento\Framework\App\Response\Http\FileFactory')->create(
                'picking_' . $date . '.pdf',
                $pdf->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }
        else
        {
            $this->messageManager->addError(__('There is no order in progress.'));
            $this->_redirect('*/*/index');
        }

    }
}
