<?php

namespace BoostMyShop\Rma\Model;


class Rma extends \Magento\Framework\Model\AbstractModel
{
    protected $_dateTime = null;

    protected $_storeManager;
    protected $_currencyFactory;
    protected $_userFactory;
    protected $_orderFactory;
    protected $_customerFactory;
    protected $_order;
    protected $_customer;

    protected $_rmaItemCollectionFactory;
    protected $_rmaItemFactory;
    protected $_rmaHistoryFactory;
    protected $_rmaHistoryCollectionFactory;
    protected $_rmaMessageFactory;
    protected $_rmaMessageCollectionFactory;
    protected $_items;
    protected $_manager;

    protected $_currency;
    protected $_refund;

    protected $_config;
    protected $_customerNotification;
    protected $_adminNotification;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('BoostMyShop\Rma\Model\ResourceModel\Rma');
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\User\Model\User $userFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \BoostMyShop\Rma\Model\ResourceModel\Rma\Item\CollectionFactory $rmaItemCollectionFactory,
        \BoostMyShop\Rma\Model\ResourceModel\Rma\History\CollectionFactory $rmaHistoryCollectionFactory,
        \BoostMyShop\Rma\Model\ResourceModel\Rma\Message\CollectionFactory $rmaMessageCollectionFactory,
        \BoostMyShop\Rma\Model\Rma\ItemFactory $rmaItemFactory,
        \BoostMyShop\Rma\Model\Rma\HistoryFactory $rmaHistoryFactory,
        \BoostMyShop\Rma\Model\Rma\MessageFactory $rmaMessageFactory,
        \BoostMyShop\Rma\Model\Config $config,
        \BoostMyShop\Rma\Model\Rma\Refund $refund,
        \BoostMyShop\Rma\Model\Rma\CustomerNotification $customerNotification,
        \BoostMyShop\Rma\Model\Rma\AdminNotification $adminNotification,
        array $data = []
    )
    {
        parent::__construct($context, $registry, null, null, $data);

        $this->_dateTime = $dateTime;
        $this->_storeManager = $storeManager;
        $this->_orderFactory = $orderFactory;
        $this->_rmaItemCollectionFactory = $rmaItemCollectionFactory;
        $this->_rmaItemFactory = $rmaItemFactory;
        $this->_rmaHistoryFactory = $rmaHistoryFactory;
        $this->_rmaMessageFactory = $rmaMessageFactory;
        $this->_rmaHistoryCollectionFactory = $rmaHistoryCollectionFactory;
        $this->_rmaMessageCollectionFactory = $rmaMessageCollectionFactory;
        $this->_userFactory = $userFactory;
        $this->_currencyFactory = $currencyFactory;
        $this->_customerFactory = $customerFactory;
        $this->_refund = $refund;
        $this->_config = $config;
        $this->_customerNotification = $customerNotification;
        $this->_adminNotification = $adminNotification;
    }

    public function beforeSave()
    {
        if (!$this->getId()) {
            $this->setrma_created_at($this->_dateTime->gmtDate());
            if (!$this->getrma_status())
                $this->setrma_status(\BoostMyShop\Rma\Model\Rma\Status::draft);
            $this->setrma_reference($this->createReference());
        }

        $this->setrma_updated_at($this->_dateTime->gmtDate());

    }

    public function afterSave()
    {
        parent::afterSave();

        if (!$this->getOrigData('rma_id'))
            $this->addHistory('RMA created');
        if ($this->getOrigData('rma_status') != $this->getrma_status())
            $this->rmaStatusChanged($this->getOrigData('rma_status'), $this->getrma_status());
    }

    public function rmaStatusChanged($from, $to)
    {
        if ($this->_config->getAutomaticCustomerNotification())
            $this->_customerNotification->notifyForStatus($this);

        $this->addHistory('Status changed to '.$to);
    }

    public function notifyCustomer()
    {
        $this->_customerNotification->notifyForStatus($this);
    }

    public function getOrder()
    {
        if ($this->getrma_order_id())
        {
            if (!$this->_order)
                $this->_order = $this->_orderFactory->create()->load($this->getrma_order_id());
            return $this->_order;
        }
    }

    public function getCustomer()
    {
        if ($this->getrma_customer_id())
        {
            if (!$this->_customer)
                $this->_customer = $this->_customerFactory->create()->load($this->getrma_customer_id());
            return $this->_customer;
        }
    }

    public function getAllItems()
    {
        if (!$this->_items)
            $this->_items = $this->_rmaItemCollectionFactory->create()->addRmaFilter($this->getId());

        return $this->_items;
    }

    public function addItem($qty, $productId, $additionnals)
    {
        $item = $this->_rmaItemFactory->create();
        $item->setri_qty($qty);
        $item->setri_product_id($productId);
        $item->setri_rma_id($this->getId());

        $item->setri_sku($item->getProduct()->getSku());
        $item->setri_name($item->getProduct()->getName());

        foreach($additionnals as $k => $v)
            $item->setData($k, $v);
        $item->save();

        return $item;
    }

    public function getCurrency()
    {
        if (!$this->_currency)
            $this->_currency = $this->_currencyFactory->create()->load($this->getpo_currency());
        return $this->_currency;
    }

    public function createReference()
    {
        $reference = $this->_dateTime->gmtDate('Ymd').'-';

        if ($this->getOrder())
            $reference .= $this->getOrder()->getIncrementId();  //todo : append incrment for orders with multiple rmas
        else
            $reference .= $this->_dateTime->gmtDate('His');

        return $reference;
    }

    public function getStore()
    {
        return $this->_storeManager->getStore($this->getrma_store_id());
    }

    public function getManager()
    {
        if (!$this->_manager)
        {
            $this->_manager = $this->_userFactory->load($this->getrma_manager());
        }
        return $this->_manager;
    }

    public function formatCurrency($value)
    {
        if (!$this->_currency)
            $this->_currency = $this->_currencyFactory->create()->load($this->getrma_currency_code());
        return $this->_currency->format($value, [], false);
    }

    public function itemBackToStock($itemId, $qty)
    {
        foreach($this->getAllItems() as $item)
        {
            if ($item->getId() == $itemId) {
                $item->backToStock($qty);
                $this->addHistory($qty.'x '.$item->getri_name().' put back to stock');
            }
        }
    }

    public function processRefund($refundData)
    {
        $creditMemo = $this->_refund->process($this, $refundData);
        if ($creditMemo)
            $this->addHistory('Credit memo #'.$creditMemo->getincrement_id().' created');
    }

    public function complete()
    {
        $this->setrma_status(\BoostMyShop\Rma\Model\Rma\Status::complete)->save();
    }

    public function getHistory()
    {
        return $this->_rmaHistoryCollectionFactory->create()->addRmaFilter($this->getId());
    }

    public function addHistory($details)
    {
        $model = $this->_rmaHistoryFactory->create();
        $model->setrh_rma_id($this->getId());
        $model->setrh_date($this->_dateTime->gmtDate());
        $model->setrh_details($details);
        $model->save();
        return $model;
    }


    public function getMessages()
    {
        return $this->_rmaMessageCollectionFactory->create()->addRmaFilter($this->getId())->setOrder('rmm_id', 'desc');
    }

    public function addMessage($author, $message)
    {
        $model = $this->_rmaMessageFactory->create();
        $model->setrmm_rma_id($this->getId());
        $model->setrmm_date($this->_dateTime->gmtDate());
        $model->setrmm_author($author);
        $model->setrmm_message($message);
        $model->save();

        $this->addHistory('New message from '.$author);

        //notify
        switch($author)
        {
            case 'admin':
                $this->_customerNotification->notifyForMessage($this, $message);
                break;
            case 'customer':
                $this->_adminNotification->notifyForMessage($this, $message);
                break;
        }

        return $model;
    }

    public function getKey()
    {
        return sha1($this->getId().' rma '.$this->getrma_created_at().' store '.$this->getrma_store_id());
    }

    public function hasInvoice()
    {
        $value = false;
        if ($this->getOrder())
        {
            if ($this->getOrder()->getInvoiceCollection()->getSize() > 0)
                $value = true;
        }
        return $value;
    }

}
