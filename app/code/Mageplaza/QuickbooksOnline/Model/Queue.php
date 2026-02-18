<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickbooksOnline
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\QuickbooksOnline\Model;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\InvoiceFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\QuickbooksOnline\Helper\Data as HelperData;
use Mageplaza\QuickbooksOnline\Model\ResourceModel\Queue as ResourceQueue;
use Mageplaza\QuickbooksOnline\Model\Source\MagentoObject;
use Mageplaza\QuickbooksOnline\Model\Source\QueueActions;
use Mageplaza\QuickbooksOnline\Model\Source\QueueStatus;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;

/**
 * Class Queue
 * @package Mageplaza\QuickbooksOnline\Model
 */
class Queue extends AbstractModel
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var InvoiceFactory
     */
    protected $invoiceFactory;

    /**
     * @var TaxRateFactory
     */
    protected $taxRateFactory;

    /**
     * @var MagentoObjectFactory
     */
    protected $magentoObjectFactory;

    /**
     * @var SyncFactory
     */
    protected $syncFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CreditmemoFactory
     */
    protected $creditmemoFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var PaymentMethodFactory
     */
    protected $paymentMethodFactory;

    /**
     * Queue constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ProductFactory $productFactory
     * @param CustomerFactory $customerFactory
     * @param OrderFactory $orderFactory
     * @param InvoiceFactory $invoiceFactory
     * @param TaxRateFactory $taxRateFactory
     * @param CreditmemoFactory $creditmemoFactory
     * @param HelperData $helperData
     * @param PaymentMethodFactory $paymentMethodFactory
     * @param SyncFactory $syncFactory
     * @param StoreManagerInterface $storeManager
     * @param MagentoObjectFactory $magentoObjectFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductFactory $productFactory,
        CustomerFactory $customerFactory,
        OrderFactory $orderFactory,
        InvoiceFactory $invoiceFactory,
        TaxRateFactory $taxRateFactory,
        CreditmemoFactory $creditmemoFactory,
        HelperData $helperData,
        PaymentMethodFactory $paymentMethodFactory,
        SyncFactory $syncFactory,
        StoreManagerInterface $storeManager,
        MagentoObjectFactory $magentoObjectFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->productFactory       = $productFactory;
        $this->customerFactory      = $customerFactory;
        $this->orderFactory         = $orderFactory;
        $this->invoiceFactory       = $invoiceFactory;
        $this->taxRateFactory       = $taxRateFactory;
        $this->creditmemoFactory    = $creditmemoFactory;
        $this->helperData           = $helperData;
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->syncFactory          = $syncFactory;
        $this->storeManager         = $storeManager;
        $this->magentoObjectFactory = $magentoObjectFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ResourceQueue::class);
    }

    /**
     * @param mixed $sync
     *
     * @return int
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function addToQueue($sync)
    {
        $countSuccess  = 0;
        $magentoObject = $sync->getMagentoObject();
        $data          = [];
        $websiteIds    = explode(',', $sync->getWebsiteIds());
        $count         = 0;

        if ($magentoObject === MagentoObject::PAYMENT_METHOD) {
            foreach ($this->helperData->getPaymentMethods() as $code => $title) {
                if ($this->isExistQueue($sync, $code) || $this->isPaymentSynced($code)) {
                    continue;
                }

                $data[] = $this->buildQueueData($code, $sync, QueueActions::CREATE, 1);
                $count++;
            }
        } else {
            $objectCollection = $this->magentoObjectFactory->getCollection($magentoObject);

            foreach ($objectCollection as $object) {
                $isValid = $sync->getConditions()->validate($object);

                if (!$isValid || $this->isExistQueue($sync, $object->getId())) {
                    continue;
                }

                $id = $this->validateWebsite($magentoObject, $websiteIds, $object);

                if ($id) {
                    $data[] = $this->buildQueueData($object->getId(), $sync, QueueActions::CREATE, $id);
                    $count++;

                    if ($count === 999) {
                        $this->insertQueues($data);
                        $countSuccess += $count;
                        $count        = 0;
                        $data         = [];
                    }
                }
            }
        }

        $this->insertQueues($data);

        return $countSuccess + $count;
    }

    /**
     * @param string $code
     *
     * @return mixed
     */
    public function isPaymentSynced($code)
    {
        return $this->paymentMethodFactory->create()->load($code, 'code')->getQuickbooksEntity();
    }

    /**
     * @param string $methodId
     * @param string $syncId
     *
     * @return int
     * @throws Exception
     */
    public function addPaymentToQueue($methodId, $syncId)
    {
        $payment      = $this->paymentMethodFactory->create()->load($methodId);
        $isExistQueue = $this->getCollection()
            ->addFieldToFilter('object', $payment->getCode())
            ->addFieldToFilter('action', QueueActions::CREATE)
            ->addFieldToFilter('quickbooks_module', QuickbooksModule::PAYMENT_METHOD)
            ->getFirstItem()->getId();

        if (!$isExistQueue && !$payment->getQuickbooksEntity()) {
            $data = [
                'object'            => $payment->getCode(),
                'magento_object'    => MagentoObject::PAYMENT_METHOD,
                'quickbooks_module' => QuickbooksModule::PAYMENT_METHOD,
                'website'           => $this->helperData->getWebsiteIdPaymentRule(),
                'action'            => QueueActions::CREATE,
                'status'            => QueueStatus::PENDING,
                'sync_id'           => $syncId
            ];
            $this->addData($data)->save();

            return 1;
        }

        return 0;
    }

    /**
     * @param Sync $sync
     * @param string $id
     *
     * @return mixed
     */
    public function isExistQueue($sync, $id)
    {
        $queueCollection = $this->getCollection()
            ->addFieldToFilter('object', $id)
            ->addFieldToFilter('quickbooks_module', $sync->getQuickbooksModule())
            ->getFirstItem();

        return $queueCollection->getId();
    }

    /**
     * @param Sync $sync
     * @param mixed $object
     *
     * @throws NoSuchEntityException
     */
    public function createQueue($sync, $object)
    {
        $id = $this->getValidateWebsiteId($sync, $object);

        if ($id) {
            $data = $this->buildQueueData($object->getId(), $sync, QueueActions::UPDATE, $id);
            $this->addData($data)->save();
        }
    }

    /**
     * @param mixed $sync
     * @param mixed $object
     *
     * @return int|string
     * @throws NoSuchEntityException
     */
    public function getValidateWebsiteId($sync, $object)
    {
        $websiteIds = explode(',', $sync->getWebsiteIds());

        return $this->validateWebsite($sync->getMagentoObject(), $websiteIds, $object);
    }

    /**
     * @param $data
     */
    public function insertQueues($data)
    {
        if ($data) {
            $this->getResource()->insertQueues($data);
        }
    }

    /**
     * @param Product|Customer|Order|Invoice $object
     * @param string $type
     *
     * @throws NoSuchEntityException
     */
    public function addDeleteObjectToQueue($object, $type)
    {
        $data = [];
        $this->checkAndBuildQueue(
            $object->getQuickbooksEntity(),
            $type,
            $object,
            QueueActions::DELETE,
            $data
        );
        $this->insertQueues($data);
    }

    /**
     * @param string $quickbooksId
     * @param string $type
     * @param mixed $object
     * @param string $action
     * @param array $data
     *
     * @throws NoSuchEntityException
     */
    public function checkAndBuildQueue($quickbooksId, $type, $object, $action, &$data)
    {
        if ($quickbooksId) {
            $sync = $this->syncFactory->create()->load($type, 'quickbooks_module');

            if ($sync->getId()) {
                $websiteIds = explode(',', $sync->getWebsiteIds());
                $id         = $this->validateWebsite($type, $websiteIds, $object);

                if ($id && $action !== QueueActions::DELETE) {
                    $data[] = $this->buildQueueData($quickbooksId, $sync, $action, $id);
                } else {
                    $data[] = $this->buildQueueData(
                        $quickbooksId . '_' . $object->getQuickbooksSyncToken(),
                        $sync,
                        $action,
                        $id
                    );
                }
            }
        }
    }

    /**
     * @param string $type
     * @param array $websiteIds
     * @param mixed $object
     *
     * @return int|string
     * @throws NoSuchEntityException
     */
    public function validateWebsite($type, $websiteIds, $object)
    {
        $id = '';

        if ($type === MagentoObject::PRODUCT && $object->getWebsiteIds()) {
            $catalogRuleWebsiteIds = array_intersect($object->getWebsiteIds(), $websiteIds);

            if ($catalogRuleWebsiteIds) {
                $id = implode(',', $catalogRuleWebsiteIds);
            }
        } else {
            $websiteId = $this->storeManager->getStore($object->getStoreId())->getWebsiteId();

            if (in_array($websiteId, $websiteIds, true)) {
                $id = $websiteId;
            }
        }

        return $id;
    }

    /**
     * @param string $object
     * @param Sync $sync
     * @param string $action
     * @param string $id
     *
     * @return array
     */
    public function buildQueueData($object, $sync, $action, $id)
    {
        return [
            'object'            => $object,
            'magento_object'    => $sync->getMagentoObject(),
            'quickbooks_module' => $sync->getQuickbooksModule(),
            'website'           => $id,
            'action'            => $action,
            'status'            => QueueStatus::PENDING,
            'sync_id'           => $sync->getId()
        ];
    }

    /**
     * @param Queue $item
     * @param UrlInterface $urlBuilder
     *
     * @return array
     */
    public function getQueueObject($item, $urlBuilder)
    {
        $magentoObject = $item['magento_object'];
        $result        = [];
        $objectId      = $item['object'];
        $orderFactory  = $this->orderFactory->create();

        switch ($magentoObject) {
            case MagentoObject::PRODUCT:
                $product        = $this->productFactory->create()->load($objectId);
                $result['name'] = $product->getSku();
                $result['url']  = $urlBuilder->getUrl('catalog/product/edit', ['id' => $product->getId()]);
                break;
            case MagentoObject::CUSTOMER:
                $customer       = $this->customerFactory->create()->load($objectId);
                $result['name'] = $customer->getName();
                $result['url']  = $urlBuilder->getUrl('customer/index/edit', ['id' => $objectId]);
                break;
            case MagentoObject::ORDER:
                $order          = $orderFactory->load($objectId);
                $result['name'] = $order->getIncrementId();
                $result['url']  = $urlBuilder->getUrl('sales/order/view', ['order_id' => $objectId]);
                break;
            case MagentoObject::INVOICE:
                $invoice        = $this->invoiceFactory->create()->load($objectId);
                $result['name'] = $invoice->getIncrementId();
                $result['url']  = $urlBuilder->getUrl('sales/invoice/view', ['invoice_id' => $objectId]);
                break;
            case MagentoObject::CREDIT_MEMO:
                $creditmemo     = $this->creditmemoFactory->createByOrder($orderFactory)->load($objectId);
                $result['name'] = $creditmemo->getIncrementId();
                $result['url']  = $urlBuilder->getUrl('sales/creditmemo/view', ['creditmemo_id' => $objectId]);
                break;
            case MagentoObject::PAYMENT_METHOD:
                $result['name'] = $item['object'];
                break;
            case MagentoObject::TAX:
                $tax            = $this->taxRateFactory->create()->load($objectId);
                $result['name'] = $tax->getCode();
                $result['url']  = $urlBuilder->getUrl('tax/rate/edit', ['rate' => $objectId]);
                break;
        }

        return $result;
    }

    /**
     * @param string $code
     * @param Sync $sync
     *
     * @throws Exception
     */
    public function createUpdatePaymentQueue($code, $sync)
    {
        $data = $this->buildQueueData($code, $sync, QueueActions::UPDATE, 1);
        $this->addData($data)->save();
    }
}
