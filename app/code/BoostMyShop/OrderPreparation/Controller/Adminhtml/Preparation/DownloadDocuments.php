<?php


namespace BoostMyShop\OrderPreparation\Controller\Adminhtml\Preparation;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class DownloadDocuments extends \BoostMyShop\OrderPreparation\Controller\Adminhtml\Preparation
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
            ->addUserFilter($userId)
            ->addWarehouseFilter($warehouseId);

        if (count($orders) > 0)
        {
            $pdf = new \Zend_Pdf();

            if ($this->_configFactory->create()->includeInvoiceInDownloadDocuments()) {
                $invoiceIds = $this->getInvoiceIds($orders);
                if (count($invoiceIds) > 0) {
                    $invoices = $this->_invoiceCollectionFactory->create()->addAttributeToSelect('*')->addFieldToFilter('entity_id', $invoiceIds);
                    $invoicePdf = $this->_invoicePdf->getPdf($invoices);
                    foreach ($invoicePdf->pages as $page)
                        $pdf->pages[] = $page;
                }
            }

            if ($this->_configFactory->create()->includeShipmentInDownloadDocuments()) {
                $shipmentIds = $this->getShipmentIds($orders);
                if (count($shipmentIds) > 0) {
                    $shipments = $this->_shipmentCollectionFactory->create()->addAttributeToSelect('*')->addFieldToFilter('entity_id', $shipmentIds);
                    $shipmentPdf = $this->_shipmentPdf->getPdf($shipments);
                    foreach ($shipmentPdf->pages as $page)
                        $pdf->pages[] = $page;
                }
            }

            $date = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->date('Y-m-d_H-i-s');
            return $this->_objectManager->get('\Magento\Framework\App\Response\Http\FileFactory')->create(
                'documents_' . $date . '.pdf',
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

    protected function getInvoiceIds($orders)
    {
        $ids = [];

        foreach($orders as $order)
        {
            if ($order->getip_invoice_id())
                $ids[] = $order->getip_invoice_id();
        }

        return $ids;
    }

    protected function getShipmentIds($orders)
    {
        $ids = [];

        foreach($orders as $order)
        {
            if ($order->getip_shipment_id())
                $ids[] = $order->getip_shipment_id();
        }

        return $ids;
    }

}
