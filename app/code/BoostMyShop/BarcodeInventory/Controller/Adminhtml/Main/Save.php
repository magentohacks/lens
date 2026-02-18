<?php
namespace BoostMyShop\BarcodeInventory\Controller\Adminhtml\Main;

use Magento\Framework\Controller\ResultFactory;

class Save extends \Magento\Backend\App\AbstractAction
{

    public function execute()
    {

        $stockUpdater = $this->_objectManager->get('\BoostMyShop\BarcodeInventory\Model\StockUpdater');

        $changes = explode(';', $this->getRequest()->getPost('changes'));
        foreach($changes as $change)
        {
            if (!$change)
                continue;

            list($productId, $qty) = explode('=', $change);

            $stockUpdater->updateStock($productId, $qty);
        }

        $this->messageManager->addSuccess(__('Inventory updated'));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/Index');

    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Boostmyshop_Barcodeinventory::Main');
    }

}