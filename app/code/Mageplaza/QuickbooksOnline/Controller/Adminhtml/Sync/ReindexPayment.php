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
namespace Mageplaza\QuickbooksOnline\Controller\Adminhtml\Sync;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\QuickbooksOnline\Controller\Adminhtml\AbstractSync;
use Mageplaza\QuickbooksOnline\Helper\Data as HelperData;
use Mageplaza\QuickbooksOnline\Helper\Sync as HelperSync;
use Mageplaza\QuickbooksOnline\Model\PaymentMethodFactory;
use Mageplaza\QuickbooksOnline\Model\QueueFactory;
use Mageplaza\QuickbooksOnline\Model\ResourceModel\PaymentMethod\Collection as PaymentCollection;
use Mageplaza\QuickbooksOnline\Model\Source\QueueStatus;
use Mageplaza\QuickbooksOnline\Model\SyncFactory;

/**
 * Class ReindexPayment
 * @package Mageplaza\QuickbooksOnline\Controller\Adminhtml\Sync
 */
class ReindexPayment extends AbstractSync
{
    /**
     * @var PaymentMethodFactory
     */
    protected $paymentMethodFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperSync
     */
    protected $helperSync;

    /**
     * @var PaymentCollection
     */
    protected $paymentCollection;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * ReindexPayment constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Filter $filter
     * @param SyncFactory $syncFactory
     * @param QueueFactory $queueFactory
     * @param PaymentMethodFactory $paymentMethodFactory
     * @param HelperData $helperData
     * @param HelperSync $helperSync
     * @param PaymentCollection $paymentCollection
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Filter $filter,
        SyncFactory $syncFactory,
        QueueFactory $queueFactory,
        PaymentMethodFactory $paymentMethodFactory,
        HelperData $helperData,
        HelperSync $helperSync,
        PaymentCollection $paymentCollection,
        ResourceConnection $resourceConnection
    ) {
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->helperData           = $helperData;
        $this->helperSync           = $helperSync;
        $this->paymentCollection    = $paymentCollection;
        $this->resourceConnection   = $resourceConnection;
        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $filter,
            $syncFactory,
            $queueFactory
        );
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $sync = $this->syncFactory->create()->load($this->getRequest()->getParam('id'));

        try {
            $count = $this->savePaymentMethod($sync);
            $this->messageManager->addSuccessMessage(__('Reindex successfully!'));

            if ($count > 0) {
                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $count));
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect('*/*/edit', ['id' => $sync->getId()]);
    }

    /**
     * @param mixed $sync
     *
     * @return int
     * @throws LocalizedException
     */
    public function savePaymentMethod($sync)
    {
        $paymentList = $this->helperData->getPaymentMethods();
        $paymentData = [];
        $connection  = $this->resourceConnection;
        $count       = 0;

        foreach ($paymentList as $code => $title) {
            if ($this->isNewTitle($code, $title)) {
                $connection->getConnection()->update(
                    $connection->getTableName('mageplaza_quickbooks_payment_method'),
                    ['title' => $title],
                    'code = "' . $code . '"'
                );
                $count += $this->checkUpdate($sync, $code);
            }

            $check = $this->paymentMethodFactory->create()->load($code, 'code');

            if (!$check->getId()) {
                $paymentData[] = compact('code', 'title');
            }
        }

        if ($paymentData) {
            $connection->getConnection()
                ->insertMultiple($connection->getTableName('mageplaza_quickbooks_payment_method'), $paymentData);
        }

        return $count;
    }

    /**
     * @param string $code
     * @param string $newTitle
     *
     * @return bool
     */
    public function isNewTitle($code, $newTitle)
    {
        $payment = $this->paymentMethodFactory->create()->load($code, 'code');

        return $payment->getId() && $payment->getTitle() !== $newTitle;
    }

    /**
     * @param mixed $sync
     * @param string $code
     *
     * @return int
     * @throws Exception
     */
    public function checkUpdate($sync, $code)
    {
        $payment = $this->paymentMethodFactory->create()->load($code, 'code')->getQuickbooksEntity();

        $hasRecordUpdate = $this->helperSync->hasQueue(
            $code,
            $sync->getQuickbooksModule(),
            $sync->getMagentoObject()
        );

        if ((!$hasRecordUpdate->getId() && $payment)
            || ($hasRecordUpdate->getId() && $hasRecordUpdate->getStatus() === QueueStatus::SUCCESS)
        ) {
            $queue = $this->queueFactory->create();
            $queue->createUpdatePaymentQueue($code, $sync);

            return 1;
        }

        return 0;
    }
}
