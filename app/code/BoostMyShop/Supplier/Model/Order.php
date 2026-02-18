<?php

namespace BoostMyShop\Supplier\Model;


class Order extends \Magento\Framework\Model\AbstractModel
{
    protected $_dateTime = null;

    protected $_orderProductCollectionFactory = null;
    protected $_productSupplierFactory = null;
    protected $_productSupplierResourceFactory = null;
    protected $_orderProductFactory = null;
    protected $_receptionFactory = null;
    protected $_storeManager;
    protected $_currencyFactory;
    protected $_productFactory;
    protected $_supplier;
    protected $_supplierFactory;
    protected $_manager;
    protected $_userFactory;
    protected $_config;
    protected $_currency;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Supplier\Model\ResourceModel\Order');
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \BoostMyShop\Supplier\Model\ResourceModel\Order\Product\CollectionFactory $orderProductCollectionFactory,
        \BoostMyShop\Supplier\Model\Order\ProductFactory $orderProductFactory,
        \BoostMyShop\Supplier\Model\Order\ReceptionFactory $receptionFactory,
        \BoostMyShop\Supplier\Model\ResourceModel\Supplier\ProductFactory $productSupplierResourceFactory,
        \BoostMyShop\Supplier\Model\Supplier\ProductFactory $productSupplierFactory,
        \BoostMyShop\Supplier\Model\SupplierFactory $supplierFactory,
        \BoostMyShop\Supplier\Model\Product $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \BoostMyShop\Supplier\Model\Config $config,
        \Magento\User\Model\User $userFactory,
        array $data = []
    )
    {
        parent::__construct($context, $registry, null, null, $data);

        $this->_dateTime = $dateTime;
        $this->_storeManager = $storeManager;
        $this->_orderProductCollectionFactory = $orderProductCollectionFactory;
        $this->_orderProductFactory = $orderProductFactory;
        $this->_supplierFactory = $supplierFactory;
        $this->_receptionFactory = $receptionFactory;
        $this->_currencyFactory = $currencyFactory;
        $this->_userFactory = $userFactory;
        $this->_productSupplierFactory = $productSupplierFactory;
        $this->_productSupplierResourceFactory = $productSupplierResourceFactory;
        $this->_config = $config;
        $this->_productFactory = $productFactory;
    }

    public function beforeSave()
    {
        if (!$this->getId())
            $this->setpo_created_at($this->_dateTime->gmtDate());

        $this->setpo_updated_at($this->_dateTime->gmtDate());

        $this->setpo_shipping_cost_base($this->getpo_shipping_cost() * $this->getpo_change_rate());
        $this->setpo_additionnal_cost_base($this->getpo_additionnal_cost() * $this->getpo_change_rate());

    }


    public function afterSave()
    {
        $this->updateDeliveryProgress();
        $this->updateTotals();
    }

        /**
     * Apply default values for fields
     *
     * @return $this
     */
    public function applyDefaultData($supplierId)
    {

        $this->setpo_status(\BoostMyShop\Supplier\Model\Order\Status::draft);
        $this->setpo_reference($this->_getResource()->getNextReference($this->getPoType()));
        $this->setpo_change_rate(1);

        $this->setpo_delivery_progress(0);

        $this->setpo_sup_id($supplierId);
        $this->setpo_tax_rate($this->getSupplier()->getsup_tax_rate());
        $this->setpo_currency($this->getSupplier()->getsup_currency());

        return $this;
    }

    /**
     *
     */
    public function getAllItems()
    {
        $collection = $this->_orderProductCollectionFactory->create();
        $collection->addOrderFilter($this->getId());
        return $collection;
    }

    public function getStore()
    {
        return $this->_storeManager->getStore(1);
    }

    public function getSupplier()
    {
        if (!$this->_supplier)
            $this->_supplier = $this->_supplierFactory->create()->load($this->getpo_sup_id());
        return $this->_supplier;
    }

    public function getManager()
    {
        if (!$this->_manager)
        {
            $this->_manager = $this->_userFactory->load($this->getpo_manager());
        }
        return $this->_manager;
    }

    public function addProduct($productId, $qty, $additionnal = [])
    {
        $obj = $this->_orderProductFactory->create();
        $obj->setpop_po_id($this->getId());
        $obj->setpop_product_id($productId);
        $obj->setpop_qty($qty);
        $obj->setpop_qty_received(0);
        $obj->setpop_tax_rate($this->getpo_tax_rate());

        $id = $this->_productSupplierResourceFactory->create()->getIdFromProductSupplier($productId, $this->getpo_sup_id());
        if ($id)
        {
            $productSupplierData = $this->_productSupplierFactory->create()->load($id);
            $obj->setpop_supplier_sku($productSupplierData->getsp_sku());

            if ($this->_config->getSetting('order_product/default_price') == 'product_supplier_association') {
                $obj->setpop_price($productSupplierData->getsp_price());
                $obj->setpop_price_base($productSupplierData->getsp_base_price());
            }
        }

        //import buying price from product cost
        if ($this->_config->getSetting('order_product/default_price') == 'product_cost') {
            $cost = $this->_productFactory->getCost($productId);
            $obj->setpop_price($cost);
            $obj->setpop_price_base($cost);
        }

        foreach($additionnal as $k => $v)
        {
            if ($v)
                $obj->setData($k, $v);
        }

        $obj->save();

        return $this;
    }

    /**
     * @param $userName
     * @param $products
     */
    public function processReception($userName, $products)
    {
        $obj = $this->_receptionFactory->create();
        $obj->init($userName, $this);
        $obj->save();

        $obj->addProducts($products);

        $this->updateQtyToReceive();
    }

    public function updateDeliveryProgress()
    {
        $this->_getResource()->updateDeliveryProgress($this->getId());
    }

    public function updateTotals()
    {
        $this->_getResource()->updateTotals($this);
    }

    public function updateQtyToReceive()
    {
        foreach($this->getAllItems() as $item)
        {
            $this->_productFactory->updateQuantityToReceive($item->getpop_product_id());
        }
    }

    public function getCurrency()
    {
        if (!$this->_currency)
            $this->_currency = $this->_currencyFactory->create()->load($this->getpo_currency());
        return $this->_currency;
    }

    public function reachesMinimumOfOrder()
    {
        if ($this->getSupplier()->getsup_minimum_of_order())
            return ($this->getSupplier()->getsup_minimum_of_order() < $this->getpo_grandtotal());
        return true;
    }

    public function reachesCarriageFree()
    {
        if ($this->getSupplier()->getsup_carriage_free_amount())
            return ($this->getSupplier()->getsup_carriage_free_amount() < $this->getpo_grandtotal());
        return true;
    }

}
