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
use Mageplaza\QuickbooksOnline\Model\Source\Status;

/**
 * Class AddToQueue
 * @package Mageplaza\QuickbooksOnline\Controller\Adminhtml\Sync
 */
class AddToQueue extends AbstractSync
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $syncCollection = $this->syncFactory->create()
            ->getCollection()
            ->addFieldToFilter('status', Status::ACTIVE)
            ->setOrder('priority', 'ASC');
        $queue          = $this->queueFactory->create();

        try {
            $count = 0;

            if ($syncCollection->getSize() > 0) {
                foreach ($syncCollection as $sync) {
                    $count += $queue->addToQueue($sync);
                }
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been added.', $count));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect('*/*/');
    }
}
