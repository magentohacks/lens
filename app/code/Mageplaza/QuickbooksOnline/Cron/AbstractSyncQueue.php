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
namespace Mageplaza\QuickbooksOnline\Cron;

use Exception;
use Mageplaza\QuickbooksOnline\Helper\Data as HelperData;
use Mageplaza\QuickbooksOnline\Helper\Sync as HelperSync;
use Mageplaza\QuickbooksOnline\Model\Config\Source\System\Schedule;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractSyncQueue
 * @package Mageplaza\QuickbooksOnline\Cron
 */
abstract class AbstractSyncQueue
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperSync
     */
    protected $helperSync;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AbstractSyncQueue constructor.
     *
     * @param HelperData $helperData
     * @param HelperSync $helperSync
     * @param LoggerInterface $logger
     */
    public function __construct(
        HelperData $helperData,
        HelperSync $helperSync,
        LoggerInterface $logger
    ) {
        $this->helperData = $helperData;
        $this->helperSync = $helperSync;
        $this->logger     = $logger;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $limitObject = $this->helperData->getLimitObjectSend();

        if ($limitObject && $this->helperData->isEnabled()) {
            $schedule = $this->helperData->getSchedule();

            if ($schedule === $this->getSchedule()) {
                $this->helperSync->setLimitObjectSend($limitObject);

                try {
                    $this->helperSync->syncs();
                } catch (Exception $e) {
                    $this->logger->debug($e->getMessage());
                }
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSchedule()
    {
        return Schedule::DAILY;
    }
}
