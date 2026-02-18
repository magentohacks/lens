<?php
namespace BoostMyShop\BarcodeInventory\Controller\Adminhtml\Main;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $config = $this->_objectManager->get('\BoostMyShop\BarcodeInventory\Model\Config\BarcodeInventory');
        if (!$config->getSetting('general/barcode_attribute'))
        {
            $this->messageManager->addError(__('Please configure barcode attribute'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('adminhtml/system_config', array('section' => 'barcodeinventory'));
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('BoostMyShop_BarcodeInventory::barcode_inventory');
        $resultPage->addBreadcrumb(__('Barcode Inventory'), __('Barcode Inventory'));
        $resultPage->getConfig()->getTitle()->prepend(__('Barcode Inventory'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Boostmyshop_Barcodeinventory::Main');
    }

}