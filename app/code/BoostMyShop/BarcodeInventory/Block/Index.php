<?php
namespace BoostMyShop\BarcodeInventory\Block;

class Index extends \Magento\Backend\Block\Template
{
    protected $_template = 'index.phtml';

    protected $_config;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \BoostMyShop\BarcodeInventory\Model\Config\BarcodeInventory $config, array $data = [])
    {
        $this->_config = $config;

        parent::__construct($context, $data);
    }

    public function getChangeWarehouseUrl()
    {
        return $this->getUrl('*/*/ChangeWarehouse', array('warehouse_id' => '[warehouse_id]'));
    }

    public function isMultipleWarehouse()
    {
        return false;
    }

    public function getWarehouses()
    {
        return [['value' => 1, 'label' => 'Default']];
    }

    public function getModes()
    {
        $obj = new \BoostMyShop\BarcodeInventory\Model\Config\Source\Modes();
        return $obj->toOptionArray(false);
    }

    public function isImmediateMode()
    {
        return false;
    }

    public function getProductInformationUrl()
    {
        return $this->getUrl('*/*/ProductInformation', ['barcode' => '[barcode]']);
    }

    public function CommitProductStockUrl()
    {

    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/Save');
    }

    public function getDefaultMode()
    {
        $value = $this->_config->getSetting('general/default_mode');

        return $value;
    }

}