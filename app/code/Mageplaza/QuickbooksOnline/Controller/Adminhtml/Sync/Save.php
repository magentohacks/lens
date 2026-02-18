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
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\QuickbooksOnline\Controller\Adminhtml\AbstractSync;
use Mageplaza\QuickbooksOnline\Helper\Data as HelperData;
use Mageplaza\QuickbooksOnline\Model\QueueFactory;
use Mageplaza\QuickbooksOnline\Model\ResourceModel\PaymentMethod\Collection as PaymentCollection;
use Mageplaza\QuickbooksOnline\Model\SyncFactory;

/**
 * Class Save
 * @package Mageplaza\QuickbooksOnline\Controller\Adminhtml\Sync
 */
class Save extends AbstractSync
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var PaymentCollection
     */
    protected $paymentCollection;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Filter $filter
     * @param SyncFactory $syncFactory
     * @param QueueFactory $queueFactory
     * @param HelperData $helperData
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
        HelperData $helperData,
        PaymentCollection $paymentCollection,
        ResourceConnection $resourceConnection
    ) {
        $this->helperData         = $helperData;
        $this->paymentCollection  = $paymentCollection;
        $this->resourceConnection = $resourceConnection;
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
        $data = $this->getRequest()->getParam('sync');

        if ($data) {
            $syncModel = $this->syncFactory->create();

            if (isset($data['id'])) {
                $syncModel->load($data['id']);
            }

            $rule = $this->getRequest()->getParam('rule', []);
            $syncModel->loadPost($rule);

            try {
                if (isset($data['mapping'])) {
                    $data['mapping'] = HelperData::jsonEncode($data['mapping']);
                }

                $syncModel->addData($data);
                $syncModel->save();
                $this->messageManager->addSuccessMessage(__('Save rule success!'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', ['id' => $syncModel->getId()]);
                }
            } catch (Exception $e) {
                $this->messageManager
                    ->addErrorMessage(__('An error occurred while saving the sync. Please try again later. '
                        . $e->getMessage()));
            }
        }

        return $this->_redirect('*/*/');
    }
}
