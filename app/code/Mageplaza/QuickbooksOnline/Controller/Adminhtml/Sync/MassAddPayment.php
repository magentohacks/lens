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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\QuickbooksOnline\Controller\Adminhtml\AbstractSync;
use Mageplaza\QuickbooksOnline\Model\Queue;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;
use Mageplaza\QuickbooksOnline\Model\Source\Status;

/**
 * Class MassAddPayment
 * @package Mageplaza\QuickbooksOnline\Controller\Adminhtml\Sync
 */
class MassAddPayment extends AbstractSync
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $sync = $this->syncFactory->create()
            ->getCollection()
            ->addFieldToFilter('status', Status::ACTIVE)
            ->addFieldToFilter('quickbooks_module', QuickbooksModule::PAYMENT_METHOD)
            ->getFirstItem();

        $params = $this->getRequest()->getParams();

        try {
            $count = 0;

            if ($sync->getData()) {
                foreach ($params['method_id'] as $methodId) {
                    /**
                     * @var Queue $queue
                     */
                    $queue = $this->queueFactory->create();
                    $count += $queue->addPaymentToQueue($methodId, $params['sync_id']);
                }

                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been added.', $count));
            } else {
                $this->messageManager->addWarningMessage(__('Rules is not active.'));
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect('*/sync/edit', ['id' => $params['sync_id']]);
    }
}
