<?php

namespace BoostMyShop\Supplier\Model\Supplier;


class Product extends \Magento\Framework\Model\AbstractModel
{
    protected $_product = null;
    protected $_productFactory = null;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Supplier\Model\ResourceModel\Supplier\Product');
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product $productFactory,
        array $data = []
    )
    {
        parent::__construct($context, $registry, null, null, $data);

        $this->_productFactory = $productFactory;
    }

    public function getProduct()
    {
        if ($this->_product == null)
        {
            $this->_product = $this->_productFactory->load($this->getsp_product_id());
        }
        return $this->_product;
    }

    public function loadByProductSupplier($productId, $supplierId)
    {
        $id = $this->_getResource()->getIdFromProductSupplier($productId, $supplierId);
        return $this->load($id);
    }

    public function afterSave()
    {
        parent::afterSave();

        //if primary applied, make sure that other are NOT primary
        if (($this->getData('sp_primary') != $this->getOrigData('sp_primary')) && ($this->getData('sp_primary'))) {
            $this->_getResource()->removeOtherPrimary($this->getsp_product_id(), $this->getsp_sup_id());
        }
    }

}
