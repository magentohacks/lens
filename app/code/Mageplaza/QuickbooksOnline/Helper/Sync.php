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
namespace Mageplaza\QuickbooksOnline\Helper;

use DateTime;
use Exception;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\InvoiceFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\ClassModelFactory as TaxFactory;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\QuickbooksOnline\Model\PaymentMethodFactory;
use Mageplaza\QuickbooksOnline\Model\Queue;
use Mageplaza\QuickbooksOnline\Model\QueueFactory;
use Mageplaza\QuickbooksOnline\Model\ResourceModel\Queue\Collection as QueueCollection;
use Mageplaza\QuickbooksOnline\Model\Source\MagentoObject;
use Mageplaza\QuickbooksOnline\Model\Source\QueueActions;
use Mageplaza\QuickbooksOnline\Model\Source\QueueStatus;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;
use Mageplaza\QuickbooksOnline\Model\Source\Status;
use Mageplaza\QuickbooksOnline\Model\Sync as SyncModel;
use Mageplaza\QuickbooksOnline\Model\SyncFactory;
use Mageplaza\QuickbooksOnline\Model\TaxRateFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AddressFactory;
use Laminas\Http\Request;

/**
 * Class Sync
 * @package Mageplaza\QuickbooksOnline\Helper
 */
class Sync extends AbstractData
{
    /**
     * Match all option in {{ }}
     */
    const PATTERN_OPTIONS = '/{{([a-zA-Z_]{0,50})(.*?)}}/si';

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * @var SyncFactory
     */
    protected $syncFactory;

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
     * @var CreditmemoFactory
     */
    protected $creditmemoFactory;

    /**
     * @var TaxRateFactory
     */
    protected $taxRateFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Mapping
     */
    protected $helperMapping;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var TaxFactory
     */
    protected $taxFactory;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var array
     */
    protected $customerRepositorys = [];

    /**
     * @var array
     */
    protected $orderRepository = [];

    /**
     * @var array
     */
    protected $invoiceRepository = [];

    /**
     * @var array
     */
    protected $creditmemoRepository = [];

    /**
     * @var array
     */
    protected $taxRateRepository = [];

    /**
     * @var array
     */
    protected $paymentMethodRepository = [];

    /**
     * @var array
     */
    protected $taxRepository = [];

    /**
     * @var int
     */
    protected $limitObjectSend = 0;

    /**
     * @var int
     */
    protected $countSyncSuccess = 0;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var PaymentMethodFactory
     */
    protected $paymentMethodFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var AddressFactory
     */
    protected $addressFactory;

    /**
     * Sync constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param QueueFactory $queueFactory
     * @param SyncFactory $syncFactory
     * @param ProductFactory $productFactory
     * @param OrderFactory $orderFactory
     * @param InvoiceFactory $invoiceFactory
     * @param CreditmemoFactory $creditmemoFactory
     * @param TaxRateFactory $taxRateFactory
     * @param Data $helperData
     * @param Mapping $helperMapping
     * @param CategoryRepository $categoryRepository
     * @param TaxFactory $taxFactory
     * @param ResourceConnection $resourceConnection
     * @param CustomerFactory $customerFactory
     * @param Escaper $escaper
     * @param PaymentMethodFactory $paymentMethodFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressFactory $addressFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        QueueFactory $queueFactory,
        SyncFactory $syncFactory,
        ProductFactory $productFactory,
        OrderFactory $orderFactory,
        InvoiceFactory $invoiceFactory,
        CreditmemoFactory $creditmemoFactory,
        TaxRateFactory $taxRateFactory,
        Data $helperData,
        Mapping $helperMapping,
        CategoryRepository $categoryRepository,
        TaxFactory $taxFactory,
        ResourceConnection $resourceConnection,
        CustomerFactory $customerFactory,
        Escaper $escaper,
        PaymentMethodFactory $paymentMethodFactory,
        CustomerRepositoryInterface $customerRepository,
        AddressFactory $addressFactory
    ) {
        $this->queueFactory         = $queueFactory;
        $this->syncFactory          = $syncFactory;
        $this->productFactory       = $productFactory;
        $this->helperData           = $helperData;
        $this->helperMapping        = $helperMapping;
        $this->categoryRepository   = $categoryRepository;
        $this->taxFactory           = $taxFactory;
        $this->resourceConnection   = $resourceConnection;
        $this->customerFactory      = $customerFactory;
        $this->invoiceFactory       = $invoiceFactory;
        $this->creditmemoFactory    = $creditmemoFactory;
        $this->taxRateFactory       = $taxRateFactory;
        $this->orderFactory         = $orderFactory;
        $this->escaper              = $escaper;
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->customerRepository = $customerRepository;
        $this->addressFactory = $addressFactory;
        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param Queue $queue
     *
     * @return Product|DataObject|mixed
     */
    public function getObjectModel($queue)
    {
        $object = new DataObject();
        $id     = $queue->getObject();

        switch ($queue->getMagentoObject()) {
            case MagentoObject::PRODUCT:
                $object = $this->productFactory->create()->load($id);
                break;
            case MagentoObject::CUSTOMER:
                $object = $this->getCustomerById($id);
                break;
            case MagentoObject::ORDER:
                $object = $this->getOrderById($id);
                break;
            case MagentoObject::INVOICE:
                $object = $this->getInvoiceById($id);
                break;
            case MagentoObject::CREDIT_MEMO:
                $object = $this->getCreditmemoById($id);
                break;
            case MagentoObject::TAX:
                $object = $this->getTaxRateById($id);
                break;
            case MagentoObject::PAYMENT_METHOD:
                $object = $this->getPaymentMethodByCode($id);
                break;
        }

        return $object;
    }

    /**
     * @param string $type
     *
     * @return string
     * @throws LocalizedException
     */
    public function getUrl($type)
    {
        $url = '';

        switch ($type) {
            case QuickbooksModule::PRODUCT:
                $url = $this->helperData->getApiUrl('item');
                break;
            case QuickbooksModule::CUSTOMER:
                $url = $this->helperData->getAPICustomerURL();
                break;
            case QuickbooksModule::ORDER:
                $url = $this->helperData->getAPIOrderURL();
                break;
            case QuickbooksModule::INVOICE:
                $url = $this->helperData->getApiUrl('invoice');
                break;
        }

        if (!$url) {
            throw new LocalizedException(__('Invalid url'));
        }

        return $url;
    }

    /**
     * @param null|string $type
     *
     * @return int
     * @throws LocalizedException
     */
    public function syncs($type = null)
    {
        $queueCollection = $this->getQueueCollectionByType($type);

        return $this->syncQueues($queueCollection);
    }

    /**
     * @param array $ids
     * @param null $type
     *
     * @return int
     * @throws LocalizedException
     */
    public function syncByIds($ids, $type = null)
    {
        $queueCollection = $this->getQueueCollectionByType($type)->addFieldToFilter('queue_id', ['IN' => $ids]);

        return $this->syncQueues($queueCollection);
    }

    /**
     * @param null|string $type
     * @param array $ids
     *
     * @return mixed
     */
    public function getAllIds($type = null, $ids = null)
    {
        return $this->getQueueCollectionByType($type, $ids)->getAllIds();
    }

    /**
     * @param mixed $queues
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQueueData($queues)
    {
        $queueData = [];

        foreach ($queues as $queue) {
            if ((int) $queue->getStatus() === QueueStatus::SUCCESS) {
                continue;
            }

            $queueData[$queue->getQuickbooksModule()][$queue->getAction()][] = $this->syncQueueObject($queue);
        }

        return $queueData;
    }

    /**
     * @param array $dataAction
     * @param string $url
     * @param array $queueLog
     *
     * @return array
     */
    public function sliceRequest($dataAction, &$url, &$queueLog)
    {
        $quickbooksData = [];
        $i              = 0;
        $limit          = 0;
        foreach ($dataAction as $record) {
            // Limit 20 record for every request
            if ($i === 20) {
                $limit++;
                $i = 0;
            }

            $queue = $record['queue'];

            if (!$url) {
                $url = $record['url'];
            }

            $quickbooksData[$limit]['data'][] = $record['mapping'];
            $queueLog[$limit]['data'][]       = $queue;
            $i++;
        }

        return $quickbooksData;
    }

    /**
     * @param array $quickbooksData
     * @param string $url
     * @param array $queueLog
     * @param string $module
     *
     * @throws LocalizedException
     */
    public function processRequest($quickbooksData, $url, $queueLog, $module = '')
    {
        $responses = [];
        $response  = [];

        if ($this->helperData->haveCompany()) {
            foreach ($quickbooksData as $data) {
                $dataRequest                     = [];
                $dataRequest['BatchItemRequest'] = $data['data'];
                $method                          = Request::METHOD_POST;

                if ($module !== QuickbooksModule::TAX) {
                    $response = $this->helperData->sendRequest($url, $method, $dataRequest);
                } else {
                    foreach ($data['data'] as $item) {
                        $response[] = $this->helperData->sendRequest($url, $method, $item);
                    }
                }
                $responses[] = $response;
            }

            $this->buildDataAndSave($responses, $queueLog, $module);
        } else {
            throw new LocalizedException(__('Synchronize failed.'));
        }
    }

    /**
     * @param array $data
     * @param string $module
     * @param int $operation
     *
     * @return mixed
     */
    public function formatData($data, $module, $operation)
    {
        foreach ($data as $key => $item) {
            foreach ($item['data'] as $childKey => $childItem) {
                if (($module === QuickbooksModule::PRODUCT || $module === QuickbooksModule::CUSTOMER)
                    && $operation === QueueActions::DELETE) {
                    $operation = QueueActions::UPDATE;
                }
                $item['data'][$childKey] = $this->batchFormat(
                    $operation,
                    ucfirst($module),
                    $childItem
                );
            }
            $data[$key]['data'] = $item['data'];
        }

        return $data;
    }

    /**
     * @param array $addr
     * @param string $find
     *
     * @return array
     */
    public function formatAddress($addr, $find)
    {
        $arr = [];

        foreach ($addr as $key => $value) {
            $newKey                = $this->checkAddressField($key, $find);
            $arr[(string) $newKey] = $value;
        }

        return $arr;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function formatObjectData($data)
    {
        $metaData         = array_slice($data, -24, 2);
        $billAddr         = array_slice($data, -22, 11);
        $shipAddr         = array_slice($data, -11, 11);
        $data             = array_diff_key($data, $metaData, $billAddr, $shipAddr);
        $data['MetaData'] = $metaData;
        $data['BillAddr'] = $this->formatAddress($billAddr, 'Bill');
        $data['ShipAddr'] = $this->formatAddress($shipAddr, 'Ship');

        return $data;
    }

    /**
     * @param string $operation
     * @param string $object
     * @param array $data
     *
     * @return array
     */
    public function batchFormat($operation, $object, $data)
    {
        switch ($operation) {
            case QueueActions::CREATE:
                $action = 'create';
                break;
            case QueueActions::UPDATE:
                $action = 'update';
                break;
            default:
                $action = 'delete';
        }

        return [
            'bId'       => 'bid',
            'operation' => $action,
            $object     => $data
        ];
    }

    /**
     * @param QueueCollection $queues
     *
     * @return int
     * @throws LocalizedException
     */
    public function syncQueues($queues)
    {
        $this->countSyncSuccess = 0;
        $queueData              = $this->getQueueData($queues);

        foreach ($queueData as $module => $moduleData) {
            $url = '';

            foreach ($moduleData as $moduleAction => $dataAction) {
                $queueLog       = [];
                $quickbooksData = $this->sliceRequest($dataAction, $url, $queueLog);

                if ($module !== QuickbooksModule::TAX) {
                    $quickbooksData = $this->formatData($quickbooksData, $module, $moduleAction);
                }

                $this->processRequest($quickbooksData, $url, $queueLog, $module);
            }
        }

        return $this->countSyncSuccess;
    }

    /**
     * @param array $responses
     * @param array $queueLog
     * @param string $module
     */
    public function buildDataAndSave($responses, $queueLog, $module = '')
    {
        $countSuccess        = 0;
        $quickbooksEntities  = [];
        $quickbooksSyncToken = [];
        $newTitle            = [];
        $queueData           = [];
        $lastQueueModel      = '';

        foreach ($responses as $responseKey => $response) {
            if ($module === QuickbooksModule::TAX) {
                $responseData = $response;
            } else {
                $responseData = $response['BatchItemResponse'];
            }

            foreach ($responseData as $field => $info) {
                $queue          = $queueLog[$responseKey]['data'][$field];
                $status         = isset($info['Fault']) ? QueueStatus::ERROR : QueueStatus::SUCCESS;
                $queueData[]    = [
                    'status'        => $status,
                    'json_response' => self::jsonEncode($info),
                    'queue_id'      => $queue->getId(),
                    'total_sync'    => $queue->getTotalSync() + 1
                ];
                $lastQueueModel = $queue;

                if ((int) $queue->getAction() === QueueActions::DELETE) {
                    $countSuccess++;
                    continue;
                }

                if ($status === QueueStatus::SUCCESS) {
                    $dataObject = $queue->getDataObject();
                    $module     = $queue->getQuickbooksModule();
                    $objId      = $dataObject->getId();

                    if ($module === QuickbooksModule::TAX) {
                        $quickbooksEntities[$module][$objId] = $info['TaxCodeId'];
                        $countSuccess++;
                    } else {
                        $type = ucfirst($module);

                        $quickbooksEntities[$module][$objId]  = $info[$type]['Id'];
                        $quickbooksSyncToken[$module][$objId] = $info[$type]['SyncToken'];

                        if ($module === QuickbooksModule::PAYMENT_METHOD) {
                            $newTitle[$module][$objId] = $info[$type]['Name'];
                        }

                        $countSuccess++;
                    }
                }
            }
        }

        if ($lastQueueModel) {
            if ($quickbooksEntities) {
                //update entity
                $lastQueueModel->getResource()->updateQuickbooksEntity($quickbooksEntities, 'quickbooks_entity');

                if ($module !== QuickbooksModule::TAX) {
                    //update sync token
                    $lastQueueModel->getResource()
                        ->updateQuickbooksEntity($quickbooksSyncToken, 'quickbooks_sync_token');

                    if ($module === QuickbooksModule::PAYMENT_METHOD) {
                        $lastQueueModel->getResource()->updatePaymentTitle($newTitle[$module]);
                    }
                }
            }

            if ($queueData) {
                $lastQueueModel->getResource()->updateQueues($queueData);
            }
        }

        $this->countSyncSuccess += $countSuccess;
    }

    /**
     * @param mixed $queue
     *
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function syncQueueObject($queue)
    {
        $queueModule     = $queue->getQuickbooksModule();
        $object          = $this->getObjectModel($queue);
        $data['mapping'] = $this->processQueueAction($queue, $object, $queueModule);
        $queue->setDataObject($object);
        $data['queue'] = $queue;
        $type          = $queueModule !== QuickbooksModule::TAX ? 'batch' : 'taxservice/taxcode';
        $data['url']   = $this->helperData->getApiUrl($type);

        return $data;
    }

    /**
     * @param mixed $queue
     * @param mixed $object
     * @param string $queueModule
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function processQueueAction($queue, $object, $queueModule)
    {
        $record      = [];
        $queueAction = (int) $queue->getAction();
        $sync        = $this->syncFactory->create()->load($queue->getSyncId());

        if ($queueAction === QueueActions::UPDATE) {
            $record              = $this->getDataMapping($sync, $object);
            $record['Id']        = $this->getQuickbooksId($object);
            $record['SyncToken'] = $this->getQuickbooksSyncToken($object);
            $record['sparse']    = true;

            if (!$record['Id'] && isset($record['MetaData'])) {
                unset($record['MetaData']);
            }
        } elseif ($queueAction === QueueActions::CREATE) {
            $record = $this->getDataMapping($sync, $object);

            if (isset($record['MetaData']) && !$record['MetaData']['CreateTime']) {
                unset($record['MetaData']);
            }
        } elseif ($queueAction === QueueActions::DELETE) {
            $obj                 = explode('_', $queue->getObject());
            $record['Id']        = $obj[0];
            $record['SyncToken'] = $obj[1];

            if ($queueModule === QuickbooksModule::PRODUCT
                || $queueModule === QuickbooksModule::CUSTOMER
            ) {
                $record['Active'] = false;
                $record['sparse'] = true;
            }
        }

        return $record;
    }

    /**
     * @param Product|Customer|Order|Invoice|Rate $object
     *
     * @return mixed
     */
    public function getQuickbooksId($object)
    {
        return $object->getData('quickbooks_entity');
    }

    /**
     * @param Product|Customer|Order|Invoice|Rate $object
     *
     * @return mixed
     */
    public function getQuickbooksSyncToken($object)
    {
        return $object->getData('quickbooks_sync_token');
    }

    /**
     * @param string $type
     * @param array $ids
     *
     * @return mixed
     */
    public function getQueueCollectionByType($type = null, $ids = null)
    {
        $queueCollection = $this->queueFactory->create()->getCollection();

        if ($type) {
            $queueCollection->addFieldToFilter('quickbooks_module', $type);
        }

        $queueCollection->addFieldToFilter(
            'status',
            [
                ['eq' => QueueStatus::PENDING],
                ['eq' => QueueStatus::ERROR]
            ]
        );

        if ($ids) {
            $queueCollection->addFieldToFilter('queue_id', ['in' => $ids]);
        }

        $queueCollection->addFieldToFilter('total_sync', ['lt' => 5]);

        if ($this->getLimitObjectSend()) {
            $queueCollection->setPageSize($this->getLimitObjectSend());
        }

        return $queueCollection;
    }

    /**
     * @param array $mappingField
     * @param SyncModel $sync
     * @param Product|Customer|Order|Invoice|TaxRateFactory $object
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function processMappingField($mappingField, $sync, $object)
    {
        $value = $mappingField['value'];

        if ($mappingField['value']) {
            $data       = $this->helperMapping->matchData($mappingField['value']);
            $dataFields = [];

            foreach ($data as $field) {
                if (!isset($dataFields[$field])) {
                    $currentValue = '';

                    switch ($sync->getMagentoObject()) {
                        case MagentoObject::CUSTOMER:
                            $currentValue = $this->processCustomerField($field, $object);
                            break;
                        case MagentoObject::PRODUCT:
                            $currentValue = $this->processProductField($field, $object);
                            break;
                        case MagentoObject::ORDER:
                        case MagentoObject::INVOICE:
                            $currentValue = $this->processOrderField($field, $object);
                            break;
                        case MagentoObject::CREDIT_MEMO:
                            $currentValue = $this->processOrderField($field, $object, MagentoObject::CREDIT_MEMO);
                            break;
                        case MagentoObject::TAX:
                            $currentValue = $this->processTaxField($field, $object);
                            break;
                    }

                    if ($currentValue && gettype($currentValue) === "string") {
                        $value = $this->replaceValue($field, $currentValue, $value);
                    }

                    $dataFields[$field] = $currentValue;
                }
            }

            if (!$value) {
                $value = $mappingField['default'];
            }

            return $this->formatValue($value, $mappingField['type']);
        }

        return $mappingField['default'];
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $value
     *
     * @return mixed
     */
    public function replaceValue($search, $replace, $value)
    {
        return str_replace((string) '{{' . $search . '}}', $replace, $value);
    }

    /**
     * @param mixed $value
     * @param string $type
     *
     * @return string
     */
    public function formatValue($value, $type)
    {
        if ($value) {
            /**
             * Replace all option match in {{}}
             */
            $value = preg_replace(self::PATTERN_OPTIONS, '', $value);
        }

        switch ($type) {
            case 'int':
                $value = (int) $value;
                break;
            case 'float':
                $value = (float) $value;
                break;
            case 'boolean':
                $value = (bool) $value;
                break;
            case 'date':
                $value = $value ? date('Y-m-d', strtotime($value)) : '';
                break;
            case 'string':
                $value = (string) $value;
        }

        return $value;
    }

    /**
     * @param string $field
     * @param string $find
     *
     * @return string|string[]
     */
    public function checkAddressField($field, $find)
    {
        if (stripos($field, $find) !== false) {
            return str_replace($find, '', $field);
        }

        return '';
    }

    /**
     * @param string $field
     * @param Customer $object
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function processCustomerField($field, $object)
    {
        if ($shippingField = $this->checkAddressField($field, 'shipping_')) {
            return $object->getDefaultShippingAddress() ?
                $object->getDefaultShippingAddress()->getData($shippingField) : $shippingField;
        }

        if ($this->checkAddressField($field, 'billing_')) {
            return $this->getDefaultBillingAddressByCustomerId($object->getData('entity_id'));
        }

        if ($field === 'website') {
            if ($object->getStore()) {
                return $object->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
            }

            $store = $this->storeManager->getStore($object->getStoreId());

            return $store ? $store->getBaseUrl(UrlInterface::URL_TYPE_WEB) : '';
        }

        if ($field === 'name') {
            return $object->getName();
        }

        return $object->getData($field);
    }

    /**
     * @param $customerId
     *
     * @return Address|null
     */
    public function getDefaultBillingAddressByCustomerId($customerId)
    {
        try {
            /** @var CustomerInterface $customer */
            $customer = $this->customerRepository->getById($customerId);

            $defaultBillingAddressId = $customer->getDefaultBilling();

            if ($defaultBillingAddressId) {
                $defaultBillingAddress = $this->addressFactory->create()->load($defaultBillingAddressId);

                return $defaultBillingAddress;
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function formatDate($value)
    {
        try {
            $date = (new DateTime($value))->format('Y-m-d');
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
            $date = '';
        }

        return $date;
    }

    /**
     * @param string $field
     * @param Product $product
     *
     * @return mixed|string
     * @throws NoSuchEntityException
     */
    public function processProductField($field, $product)
    {
        if ($field === 'category_ids') {
            return $this->getCategoryName($product->getData($field));
        }

        if ($field === 'tax_class_id') {
            return $this->getTaxName($product->getData($field));
        }

        if ($field === 'qty' && $product->getData('quantity_and_stock_status')) {
            $stock = $product->getData('quantity_and_stock_status');
            if ($stock && is_array($stock) && isset($stock['qty'])) {
                return $stock['qty'];
            }

            if ($product->getData('stock_data') && is_array($product->getData('stock_data'))) {
                return $product->getData('stock_data')['qty'];
            }
        }

        if ($product->getResource()) {
            if ($productAttribute = $product->getResource()->getAttribute($field)) {
                return $productAttribute->getFrontend()->getValue($product);
            }
        }

        return $product->getData($field);
    }

    /**
     * @param string $field
     * @param TaxRateFactory $object
     *
     * @return mixed
     */
    public function processTaxField($field, $object)
    {
        return $object->getData($field);
    }

    /**
     * @param string $field
     * @param Order|Invoice $object
     * @param string $type
     *
     * @return mixed
     */
    public function processOrderField($field, $object, $type = '')
    {
        if ($type === MagentoObject::CREDIT_MEMO) {
            $object = $this->getCreditmemoById($object->getId());
        }

        if ($field !== 'shipping_description'
            && $field !== 'shipping_method'
            && $shippingField = $this->checkAddressField($field, 'shipping_')
        ) {
            if ($type === MagentoObject::CREDIT_MEMO) {
                $object = $this->getInvoiceById($object->getInvoiceId());
            }

            return $object->getShippingAddress() ?
                $object->getShippingAddress()->getData($shippingField) : $shippingField;
        }

        if ($billingField = $this->checkAddressField($field, 'billing_')) {
            if ($type === MagentoObject::CREDIT_MEMO) {
                $object = $this->getInvoiceById($object->getInvoiceId());
            }

            return $object->getShippingAddress() ?
                $object->getBillingAddress()->getData($billingField) : $billingField;
        }

        return $object->getData($field);
    }

    /**
     * @param Product|Customer|Order|Invoice|Rate|DataObject|array $oldObject
     * @param Product|Customer|Order|Invoice|Rate $currentObject
     * @param string $type
     * @param bool $isAddressCustomer
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function updateObject($oldObject, $currentObject, $type, $isAddressCustomer = false)
    {
        $sync = $this->getSyncRule($currentObject, $type);

        if ($sync && $sync->getId()) {
            if (is_array($oldObject)) {
                $oldObject = new DataObject($oldObject);
            }
            if (!$isAddressCustomer) {
                $oldData     = $this->getDataMapping($sync, $oldObject);
                $currentData = $this->getDataMapping($sync, $currentObject);
                $result      = ($oldData === $currentData);
            } else {
                $result = false;
            }

            if (!$result || $isAddressCustomer === true) {
                $hasRecordUpdate = $this->hasQueue(
                    $currentObject->getId(),
                    $sync->getQuickbooksModule(),
                    $sync->getMagentoObject()
                );

                if ($hasRecordUpdate->getId()
                    && $hasRecordUpdate->getValidateWebsiteId($sync, $currentObject)
                    && $hasRecordUpdate->getSyncId() !== $sync->getId()
                ) {
                    $hasRecordUpdate->setSyncId($sync->getId())->save();
                } else {
                    if (!$hasRecordUpdate->getId()
                        || ($hasRecordUpdate->getId() && $hasRecordUpdate->getStatus() === QueueStatus::SUCCESS)) {
                        /**
                         * @var Queue $queue
                         */
                        $queue = $this->queueFactory->create();
                        $queue->createQueue($sync, $currentObject);
                    }
                }
            }
        }
    }

    /**
     * @param string $type
     * @param Product|Customer|Order|Invoice|Rate $object
     *
     * @return bool|Queue
     * @throws NoSuchEntityException
     */
    public function addObjectToQueue($type, $object)
    {
        $sync     = $this->getSyncRule($object, $type);
        $objectId = $object->getId();

        if ($sync && $sync->getId()
            && !$this->hasQueue($objectId, $sync->getQuickbooksModule(), $sync->getMagentoObject(), false)->getId()
        ) {
            /**
             * @var Queue $queue
             */
            $queue      = $this->queueFactory->create();
            $websiteIds = explode(',', $sync->getWebsiteIds());
            $id         = $queue->validateWebsite($sync->getMagentoObject(), $websiteIds, $object);

            if ($id) {
                $data = $queue->buildQueueData($object->getId(), $sync, QueueActions::CREATE, $id);
                $queue->addData($data)->save();

                return $queue;
            }
        }

        return false;
    }

    /**
     * @param Product|Customer|Order|Invoice|Rate $object
     * @param string $quickbooksModule
     *
     * @return mixed
     */
    public function getSyncRule($object, $quickbooksModule)
    {
        $syncs = $this->syncFactory->create()->getCollection()
            ->addFieldToFilter('status', Status::ACTIVE)
            ->addFieldToFilter('quickbooks_module', $quickbooksModule)
            ->setOrder('priority', 'ASC');

        foreach ($syncs as $sync) {
            if ($sync->getConditions()->validate($object)) {
                return $sync;
            }
        }

        return null;
    }

    /**
     * @param string $objectId
     * @param string $quickbooksModule
     * @param string $magentoObject
     *
     * @return mixed
     */
    public function hasRecordUpdate($objectId, $quickbooksModule, $magentoObject)
    {
        return $this->hasQueue($objectId, $quickbooksModule, $magentoObject);
    }

    /**
     * @param string $objectId
     * @param string $quickbooksModule
     * @param string $magentoObject
     * @param bool $isUpdate
     *
     * @return mixed
     */
    public function hasQueue($objectId, $quickbooksModule, $magentoObject, $isUpdate = true)
    {
        $queue = $this->queueFactory->create()
            ->getCollection()
            ->addFieldToFilter('object', $objectId)
            ->addFieldToFilter('quickbooks_module', $quickbooksModule)
            ->addFieldToFilter('magento_object', $magentoObject);

        if ($isUpdate) {
            $queue->addFieldToFilter('action', QueueActions::UPDATE);
            $queue->addFieldToFilter('status', QueueStatus::PENDING);
        } else {
            $queue->addFieldToFilter('action', QueueActions::CREATE);
        }

        return $queue->getFirstItem();
    }

    /**
     * @param mixed $sync
     * @param mixed $object
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getDataMapping($sync, $object)
    {
        $magentoObject = $sync->getMagentoObject();
        $mapping       = self::jsonDecode($sync->getMapping());
        $record        = [];

        if ($magentoObject === MagentoObject::PAYMENT_METHOD) {
            $record['Name'] = $this->helperData->getPaymentName($object->getCode());
        } else {
            foreach ($mapping as $field => $mappingField) {
                $mappingValue = $this->processMappingField($mappingField, $sync, $object);

                if ($mappingField['value'] === '{{description}}') {
                    $record[$field] = $this->escaper->escapeHtml($mappingValue);
                } else {
                    $record[$field] = $mappingValue;
                }
            }
        }

        if ($magentoObject === MagentoObject::PRODUCT) {
            return $this->setProductRecord($record);
        }

        if ($magentoObject === MagentoObject::CUSTOMER) {
            return $this->setCustomerRecord($record);
        }

        $objectOrder = [MagentoObject::ORDER, MagentoObject::INVOICE, MagentoObject::CREDIT_MEMO];

        if (in_array($magentoObject, $objectOrder, true)) {
            return $this->setOrderRecord($record, $magentoObject, $object);
        }

        if ($magentoObject === MagentoObject::TAX) {
            return $this->setTaxRecord($record);
        }

        return $record;
    }

    /**
     * @param array $record
     * @param string $magentoObject
     * @param mixed $object
     *
     * @return array|mixed
     */
    public function setOrderRecord($record, $magentoObject, $object)
    {
        $customerId             = '';
        $record                 = $this->formatObjectData($record);
        $record['Line']         = $this->processItems($magentoObject, $object);
        $record['BillEmail']    = ['Address' => $record['BillEmail']];
        $record['CustomerMemo'] = ['value' => $record['CustomerMemo']];
        $record['CurrencyRef']  = ['value' => $record['CurrencyRef']];

        switch ($magentoObject) {
            case MagentoObject::INVOICE:
                $orderId               = $object->getOrderId();
                $record                = $this->buildTaxData($orderId, $record);
                $customerId            = $object->getOrder()->getCustomerId();
                $record['BillEmailCc'] = ['Address' => $record['BillEmailCc']];
                break;
            case MagentoObject::CREDIT_MEMO:
                $orderId              = $object->getOrderId();
                $record               = $this->buildTaxData($orderId, $record);
                $customerId           = $this->getOrderById($orderId)->getCustomerId();
                $record['InvoiceRef'] = ['value' => $record['InvoiceRef']];
                break;
            case MagentoObject::ORDER:
                $record      = $this->buildTaxData($object->getId(), $record);
                $customerId  = $object->getCustomerId();
                $orderObject = $this->orderFactory->create()->load($object->getEntityId());

                if ($orderObject->getPayment()) {
                    $paymentCode = $orderObject->getPayment()->getMethod();

                    if ($paymentId = $this->getPaymentMethodByCode($paymentCode)->getQuickbooksEntity()) {
                        $record['PaymentMethodRef'] = ['value' => $paymentId];
                    }
                }

                break;
        }

        if ($customerId) {
            $customer   = $this->getCustomerById($customerId);
            $customerId = $customer->getQuickbooksEntity();
        } else {
            $customerId = $this->helperData->getCustomerGuestId();
        }

        $record['CustomerRef'] = ['value' => $customerId];
        $record['sparse']      = true;

        return $record;
    }

    /**
     * @param array $record
     *
     * @return mixed
     */
    public function setProductRecord($record)
    {
        if (!isset($record['Active']) || $record['Active'] === false) {
            $record['Active'] = false;
        } else {
            $record['Active'] = true;
        }

        $record['IncomeAccountRef']['value']  = $this->helperData->getIncomeAccount();
        $record['AssetAccountRef']['value']   = $this->helperData->getAssetAccount();
        $record['ExpenseAccountRef']['value'] = $this->helperData->getExpenseAccount();

        return $record;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public function setCustomerRecord($record)
    {
        $record = $this->formatObjectData($record);

        if (!isset($record['Active']) || $record['Active'] === false) {
            $record['Active'] = false;
        } else {
            $record['Active'] = true;
        }

        $record['PrimaryEmailAddr'] = ['Address' => $record['PrimaryEmailAddr']];
        $record['Mobile']           = ['FreeFormNumber' => $record['Mobile']];
        $record['PrimaryPhone']     = ['FreeFormNumber' => $record['PrimaryPhone']];
        $record['Fax']              = ['FreeFormNumber' => $record['Fax']];
        $record['WebAddr']          = ['URI' => $record['WebAddr']];

        return $record;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public function setTaxRecord($record)
    {
        $rateDetail = array_slice($record, 1, 3);
        $rateDetail += ['TaxAgencyId' => $this->helperData->getTaxAgency()];

        return [
            'TaxCode'        => $record['TaxCode'],
            'TaxRateDetails' => [
                $rateDetail
            ]
        ];
    }

    /**
     * @param string $orderId
     * @param array $record
     *
     * @return mixed
     */
    public function buildTaxData($orderId, $record)
    {
        $connect = $this->resourceConnection;

        $code = $connect->getConnection()
            ->select()
            ->from($connect->getTableName('sales_order_tax'))
            ->where('order_id = ?', $orderId);

        if ($connect->getConnection()->fetchRow($code)) {
            $taxCode = $connect->getConnection()->fetchRow($code)['code'];

            if ($taxCode) {
                $taxQuickbooksId        = $this->taxRateFactory->create()->load($taxCode, 'code')->getQuickbooksEntity();
                $record['TxnTaxDetail'] = [
                    'TxnTaxCodeRef' => ['value' => $taxQuickbooksId],
                    'TotalTax'      => $record['TxnTaxDetail']
                ];

                return $record;
            }
        }

        unset($record['TxnTaxDetail']);

        return $record;
    }

    /**
     * @param string $magentoObject
     * @param Order|Invoice $object
     *
     * @return array
     */
    public function processItems($magentoObject, $object)
    {
        $productDetails = [];
        $count          = 1;

        if ($object->getItems()) {
            foreach ($object->getItems() as $item) {
                if (!$item->hasRowTotal()) {
                    continue;
                }

                $product          = $this->productFactory->create()->load($item->getProductId());
                $qty              = $magentoObject === MagentoObject::ORDER ? $item->getQtyOrdered() : $item->getQty();
                $productDetails[] = [
                    'LineNum'             => $count,
                    'DetailType'          => 'SalesItemLineDetail',
                    'SalesItemLineDetail' => [
                        'ItemRef'    => [
                            'value' => $product->getQuickbooksEntity()
                        ],
                        'Qty'        => $qty,
                        'UnitPrice'  => (float) $item->getPrice(),
                        'TaxCodeRef' => [
                            'value' => '5'
                        ]
                    ],
                    'Amount'              => (float) $item->getPrice() * $qty
                ];
                $count++;
            }

            if (!is_null($object) && (float)$object->getShippingAmount()) {
                $productDetails[] = [
                    'DetailType'          => 'SalesItemLineDetail',
                    'SalesItemLineDetail' => [
                        'TaxCodeRef' => [
                            'value' => '5'
                        ]
                    ],
                    'Amount'              => (float)$object->getShippingAmount()
                ];
            }
        }

        return $productDetails;
    }

    /**
     * @param string $taxId
     *
     * @return mixed
     */
    public function getTaxName($taxId)
    {
        if (!isset($this->taxRepository[$taxId])) {
            $tax                         = $this->taxFactory->create()->load($taxId);
            $this->taxRepository[$taxId] = $tax;
        }

        return $this->taxRepository[$taxId]->getClassName();
    }

    /**
     * @param array $categoryIds
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCategoryName($categoryIds)
    {
        $names = [];

        if ($categoryIds && is_array($categoryIds)) {
            foreach ($categoryIds as $id) {
                $category = $this->categoryRepository->get($id);

                if ($category) {
                    $names[] = $category->getName();
                }
            }
        }

        return implode(', ', $names);
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function getCustomerById($id)
    {
        if (!isset($this->customerRepositorys[$id])) {
            /**
             * @var Customer $customer
             */
            $customer = $this->customerFactory->create();
            $customer->load($id);
            $this->customerRepositorys[$id] = $customer;
        }

        return $this->customerRepositorys[$id];
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function getOrderById($id)
    {
        if (!isset($this->orderRepository[$id])) {
            $order = $this->orderFactory->create();
            $order->load($id);
            $this->orderRepository[$id] = $order;
        }

        return $this->orderRepository[$id];
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function getInvoiceById($id)
    {
        if (!isset($this->invoiceRepository[$id])) {
            $invoice = $this->invoiceFactory->create();
            $invoice->load($id);
            $this->invoiceRepository[$id] = $invoice;
        }

        return $this->invoiceRepository[$id];
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function getCreditmemoById($id)
    {
        if (!isset($this->creditmemoRepository[$id])) {
            $creditmemo = $this->creditmemoFactory->createByOrder($this->orderFactory->create());
            $creditmemo->load($id);
            $this->creditmemoRepository[$id] = $creditmemo;
        }

        return $this->creditmemoRepository[$id];
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function getTaxRateById($id)
    {
        if (!isset($this->taxRateRepository[$id])) {
            $taxRate = $this->taxRateFactory->create();
            $taxRate->load($id);
            $this->taxRateRepository[$id] = $taxRate;
        }

        return $this->taxRateRepository[$id];
    }

    /**
     * @param string $code
     *
     * @return mixed
     */
    public function getPaymentMethodByCode($code)
    {
        if (!isset($this->paymentMethodRepository[$code])) {
            $payment = $this->paymentMethodFactory->create();
            $payment->load($code, 'code');
            $this->paymentMethodRepository[$code] = $payment;
        }

        return $this->paymentMethodRepository[$code];
    }

    /**
     * @param string $number
     */
    public function setLimitObjectSend($number)
    {
        $this->limitObjectSend = $number;
    }

    /**
     * @return int
     */
    public function getLimitObjectSend()
    {
        return $this->limitObjectSend;
    }
}
