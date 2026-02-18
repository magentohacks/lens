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
namespace Mageplaza\QuickbooksOnline\Controller\Adminhtml;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\InvoiceFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\QuickbooksOnline\Helper\Sync as HelperSync;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;
use Mageplaza\QuickbooksOnline\Model\Sync;
use Mageplaza\QuickbooksOnline\Model\TaxRateFactory;

/**
 * Class AbstractMassAction
 * @package Mageplaza\QuickbooksOnline\Controller\Adminhtml
 */
class AbstractMassAction extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CustomerCollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var HelperSync
     */
    protected $helperSync;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var InvoiceCollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     * @var CreditmemoCollectionFactory
     */
    protected $creditmemoCollectionFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

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
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * AbstractMassAction constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param HelperSync $helperSync
     * @param ProductCollectionFactory $productCollectionFactory
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param InvoiceCollectionFactory $invoiceCollectionFactory
     * @param CreditmemoCollectionFactory $creditmemoCollectionFactory
     * @param ProductFactory $productFactory
     * @param OrderFactory $orderFactory
     * @param InvoiceFactory $invoiceFactory
     * @param CreditmemoFactory $creditmemoFactory
     * @param TaxRateFactory $taxRateFactory
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CustomerCollectionFactory $customerCollectionFactory,
        HelperSync $helperSync,
        ProductCollectionFactory $productCollectionFactory,
        OrderCollectionFactory $orderCollectionFactory,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        CreditmemoCollectionFactory $creditmemoCollectionFactory,
        ProductFactory $productFactory,
        OrderFactory $orderFactory,
        InvoiceFactory $invoiceFactory,
        CreditmemoFactory $creditmemoFactory,
        TaxRateFactory $taxRateFactory,
        CustomerFactory $customerFactory
    ) {
        $this->filter                      = $filter;
        $this->customerCollectionFactory   = $customerCollectionFactory;
        $this->helperSync                  = $helperSync;
        $this->productCollectionFactory    = $productCollectionFactory;
        $this->orderCollectionFactory      = $orderCollectionFactory;
        $this->invoiceCollectionFactory    = $invoiceCollectionFactory;
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
        $this->productFactory              = $productFactory;
        $this->orderFactory                = $orderFactory;
        $this->invoiceFactory              = $invoiceFactory;
        $this->creditmemoFactory           = $creditmemoFactory;
        $this->taxRateFactory              = $taxRateFactory;
        $this->customerFactory             = $customerFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->getCollection());

        try {
            $count = 0;

            foreach ($collection->getItems() as $item) {
                if ($item instanceof Product) {
                    $item->load($item->getId());
                }

                /**
                 * @var Sync $sync
                 */
                $sync             = $this->helperSync->getSyncRule($item, $this->getType());
                $quickbooksEntity = $item->getQuickbooksEntity();

                if ($sync && !$quickbooksEntity && $sync->getId()) {
                    $queue = $this->helperSync->addObjectToQueue($this->getType(), $item);
                    if ($queue) {
                        $count++;
                    }
                }
            }

            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been added.', $count));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect($this->getRedirectUrl());
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->customerCollectionFactory->create();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return QuickbooksModule::CUSTOMER;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return 'customer/';
    }

    /**
     * @param string $id
     *
     * @return ResponseInterface
     */
    public function addToQueue($id = '')
    {
        $id    = $id ?: $this->getRequest()->getParam('id');
        $model = $this->getModel()->load($id);

        if ($model->getId()) {
            if (!$model->getQuickbooksEntity()) {
                try {
                    $sync = $this->helperSync->getSyncRule($model, $this->getType());

                    if ($sync && $sync->getId()) {
                        $this->helperSync->addObjectToQueue($this->getType(), $model);
                        $this->messageManager->addSuccessMessage(__('Add to Quickbooks queue success.'));
                    } else {
                        $this->messageManager->addErrorMessage(
                            __('Synchronization rule cannot be matched.')
                        );
                    }
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            } else {
                $this->messageManager->addErrorMessage(__('Quickbooks Entity is exist.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Invalid object.'));
        }

        return $this->_redirect($this->getRedirectUrl(), $this->getParamUrl($id));
    }

    /**
     * @return $this
     */
    public function getModel()
    {
        return $this;
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function getParamUrl($id)
    {
        return ['id' => $id];
    }
}
