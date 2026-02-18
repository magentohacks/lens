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
namespace Mageplaza\QuickbooksOnline\Controller\Adminhtml\Queue;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\QuickbooksOnline\Helper\Sync as HelperSync;

/**
 * Class EstimateSync
 * @package Mageplaza\QuickbooksOnline\Controller\Adminhtml\Queue
 */
class EstimateSync extends Action
{
    /**
     * @var HelperSync
     */
    protected $helperSync;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * EstimateSync constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param HelperSync $helperSync
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        HelperSync $helperSync
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperSync        = $helperSync;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $type        = $this->getRequest()->getParam('type');
        $selectedIds = $this->getRequest()->getParam('ids');
        $result      = [];

        try {
            if ($type) {
                if ($type === 'all') {
                    $type = '';
                }
                $ids             = $this->helperSync->getAllIds($type, $selectedIds);
                $result['ids']   = $ids;
                $result['total'] = count($ids);

                if ($result['total'] === 0) {
                    $result['message'] = __('Data not found when trying to synchronize.');
                }

                $result['status'] = true;
            } else {
                $result['status']  = false;
                $result['message'] = __('Please select type sync.');
            }
        } catch (Exception $e) {
            $result['status']  = false;
            $result['message'] = __($e->getMessage());
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($result);

        return $resultJson;
    }
}
