<?php

namespace BoostMyShop\Supplier\Model\Order;


class Product extends \Magento\Framework\Model\AbstractModel
{
    protected $_product = null;
    protected $_order = null;

    protected $_productFactory = null;
    protected $_orderFactory = null;
    protected $_productHelper = null;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Supplier\Model\ResourceModel\Order\Product');
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \BoostMyShop\Supplier\Model\Order $orderFactory,
        \BoostMyShop\Supplier\Model\Product $productHelper,
        array $data = []
    )
    {
        parent::__construct($context, $registry, null, null, $data);

        $this->_productFactory = $productFactory;
        $this->_orderFactory = $orderFactory;
        $this->_productHelper = $productHelper;
    }

    public function beforeSave()
    {
        if (!$this->getId())
        {
            $this->setpop_sku($this->getProduct()->getSku());
            $this->setpop_name($this->getProduct()->getName());
        }

        //update total information
        $this->setpop_price_base($this->getpop_price() * $this->getOrder()->getpo_change_rate());
        $this->setpop_subtotal($this->getpop_qty() * $this->getpop_price());
        $this->setpop_subtotal_base($this->getpop_qty() * $this->getpop_price_base());

        $this->setpop_tax($this->getpop_subtotal() / 100 * $this->getpop_tax_rate());
        $this->setpop_tax_base($this->getpop_subtotal_base() / 100 * $this->getpop_tax_rate());

        $this->setpop_grandtotal($this->getpop_subtotal() + $this->getpop_tax());
        $this->setpop_grandtotal_base($this->getpop_subtotal_base() + $this->getpop_tax_base());
    }

    public function afterDelete()
    {
        $this->_productHelper->updateQuantityToReceive($this->getpop_product_id());
    }


    public function getProduct()
    {
        if ($this->_product == null)
        {
            $this->_product = $this->_productFactory->create()->load($this->getpop_product_id());
        }
        return $this->_product;
    }

    public function getOrder()
    {
        if ($this->_order == null)
        {
            $this->_order = $this->_orderFactory->load($this->getpop_po_id());
        }
        return $this->_order;
    }

    public function getPendingQty()
    {
        $value = $this->getPopQty() - $this->getPopQtyReceived();
        if ($value < 0)
            $value = 0;
        return $value;
    }

}
