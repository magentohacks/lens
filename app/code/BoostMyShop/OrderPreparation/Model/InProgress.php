<?php

namespace BoostMyShop\OrderPreparation\Model;


class InProgress extends \Magento\Framework\Model\AbstractModel
{
    protected $_storeManager;
    protected $_userFactory;
    protected $_orderFactory;
    protected $_order;
    protected $_inProgressItemCollectionFactory;
    protected $_inProgressItemFactory;
    protected $_shipmentHelperFactory;
    protected $_invoiceHelperFactory;
    protected $_invoiceFactory;
    protected $_shipmentFactory;
    protected $_logger;

    const STATUS_NEW = 'new';
    const STATUS_PICKED = 'picked';
    const STATUS_PACKED = 'packed';
    const STATUS_SHIPPED = 'shipped';

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\User\Model\User $userFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\Shipment $shipmentFactory,
        \Magento\Sales\Model\Order\Invoice $invoiceFactory,
        \BoostMyShop\OrderPreparation\Model\ResourceModel\InProgress\Item\CollectionFactory $inProgressItemCollectionFactory,
        \BoostMyShop\OrderPreparation\Model\InProgress\ItemFactory $inProgressItemFactory,
        \BoostMyShop\OrderPreparation\Model\InProgress\ShipmentFactory $shipmentHelperFactory,
        \BoostMyShop\OrderPreparation\Model\InProgress\InvoiceFactory $invoiceHelperFactory,
        \BoostMyShop\OrderPreparation\Helper\Logger $logger,
        array $data = []
    )
    {
        parent::__construct($context, $registry, null, null, $data);

        $this->_storeManager = $storeManager;
        $this->_userFactory = $userFactory;
        $this->_orderFactory = $orderFactory;
        $this->_inProgressItemCollectionFactory = $inProgressItemCollectionFactory;
        $this->_inProgressItemFactory = $inProgressItemFactory;
        $this->_shipmentHelperFactory = $shipmentHelperFactory;
        $this->_invoiceHelperFactory = $invoiceHelperFactory;
        $this->_invoiceFactory = $invoiceFactory;
        $this->_shipmentFactory = $shipmentFactory;
        $this->_logger = $logger;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\OrderPreparation\Model\ResourceModel\InProgress');
    }

    public function beforeDelete()
    {
        $this->_inProgressItemCollectionFactory->create()->deleteForOrder($this->getip_order_id());
        return parent::beforeDelete();
    }

    public function getStore()
    {
        return $this->_storeManager->getStore($this->getip_store_id());
    }

    public function getOperatorName()
    {
        return $this->_userFactory->load($this->getip_user_id())->getUsername();
    }

    public function getOrder()
    {
        if (!$this->_order)
        {
            $this->_order = $this->_orderFactory->create()->load($this->getip_order_id());
        }
        return $this->_order;
    }

    public function getShipment()
    {
        if ($this->getip_shipment_id())
        {
            return $this->_shipmentFactory->load($this->getip_shipment_id());
        }
    }

    public function getInvoice()
    {
        if ($this->getip_invoice_id())
        {
            return $this->_invoiceFactory->load($this->getip_invoice_id());
        }
    }

    public function addProduct($orderItemId, $qty)
    {
        $obj = $this->_inProgressItemFactory->create();
        $obj->setipi_order_id($this->getip_order_id());
        $obj->setipi_order_item_id($orderItemId);
        $obj->setipi_qty($qty);
        $obj->save();

        return $obj;
    }

    public function getAllItems()
    {
        return $this->_inProgressItemCollectionFactory->create()->addOrderFilter($this->getip_order_id())->joinOrderItem();
    }

    public function getLabel()
    {
        return "#".$this->getOrder()->getincrementId()." (".$this->getOrder()->getcustomer_firstname().' '.$this->getOrder()->getcustomer_lastname().") - ".$this->getip_status();
    }

    public function loadByShipmentReference($shipmentReference)
    {
        $id = $this->_getResource()->getIdFromShipmentReference($shipmentReference);
        return $this->load($id);
    }

    /**
     *
     */
    public function pack($createShipment, $createInvoice, $quantities = null)
    {
        if ($createInvoice)
        {
            if ($this->getOrder()->canInvoice())
            {
                $this->_logger->log('Create invoice for order #'.$this->getOrder()->getIncrementId());

                $invoice = $this->_invoiceHelperFactory->create()->createInvoice($this, $quantities);
                $this->setip_invoice_id($invoice->getId())->save();
            }
        }
        else
            $this->_logger->log('DO NOT Create invoice for order #'.$this->getOrder()->getIncrementId());

        if ($createShipment)
        {
            $this->_logger->log('Create shipment for order #'.$this->getOrder()->getIncrementId());

            $shipment = $this->_shipmentHelperFactory->create()->createShipment($this, $quantities);
            $this->setip_shipment_id($shipment->getId())->save();
        }
        else
            $this->_logger->log('DO NOT Create shipment for order #'.$this->getOrder()->getIncrementId());

        $this->setip_status(self::STATUS_PACKED)->save();

        return $this;
    }

    public function addTracking($trackingNumber)
    {
        if (!$this->getShipment())
            throw new \Exception('No shipment available, unable to add tracking number');

        //try to update existing tracking number
        if ($trackingNumber)
        {
            foreach($this->getShipment()->getTracksCollection() as $tracking)
            {
                $tracking->setNumber($trackingNumber)->save();
                return;
            }

            //no tracking to update, add it
            $this->_shipmentHelperFactory->create()->addTracking($this->getShipment(), $trackingNumber, '', '');
        }
        $this->notifyCustomer();

        $this->setip_status(self::STATUS_SHIPPED)->save();
        return $this;
    }

    public function getTrackingNumber()
    {
        if ($this->getShipment())
        {
            foreach($this->getShipment()->getTracksCollection() as $tracking)
                return $tracking->getNumber();
        }
    }

    public function notifyCustomer()
    {
        $this->_shipmentHelperFactory->create()->notifyCustomer($this->getShipment());

    }


    /**
     * @param $orderInProgress
     */
    public function getDatasForExport()
    {
        $datas = [];

        foreach($this->getData() as $k => $v)
        {
            if ((!is_array($v)) && (!is_object($v)))
                $datas['preparation.'.$k] = $v;
        }

        foreach($this->getOrder()->getData() as $k => $v)
        {
            if ((!is_array($v)) && (!is_object($v)))
                $datas['order.'.$k] = $v;
        }

        foreach($this->getOrder()->getShippingAddress()->getData() as $k => $v)
        {
            if ((!is_array($v)) && (!is_object($v)))
                $datas['shippingaddress.'.$k] = $v;
        }

        if ($this->getShipment()) {
            foreach ($this->getShipment()->getData() as $k => $v) {
                if ((!is_array($v)) && (!is_object($v)))
                    $datas['shipment.' . $k] = $v;
            }
        }

        if ($this->getInvoice()) {
            foreach ($this->getInvoice()->getData() as $k => $v) {
                if ((!is_array($v)) && (!is_object($v)))
                    $datas['invoice.' . $k] = $v;
            }
        }

        return $datas;
    }

    /**
     *
     */
    public function getEstimatedWeight()
    {
        $weight = 0;
        foreach($this->getAllItems() as $item)
        {
            $weight += $item->getWeight() * $item->getipi_qty();
        }
        return $weight;
    }


}
