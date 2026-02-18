<?php
namespace BoostMyShop\OrderPreparation\Block\Packing;

class Products extends AbstractBlock
{
    protected $_template = 'OrderPreparation/Packing/Products.phtml';

    public function getProducts()
    {
        return $this->currentOrderInProgress()->getAllItems();
    }

    public function getProductLocation($productId)
    {
        return $this->_product->create()->getLocation($productId, $this->_preparationRegistry->getCurrentWarehouseId());
    }

    public function getProductImageUrl($productId)
    {
        return $this->_product->create()->getImageUrl($productId);
    }

    public function getBarcode($productId)
    {
        return $this->_product->create()->getBarcode($productId);
    }

    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/confirmPacking');
    }

}