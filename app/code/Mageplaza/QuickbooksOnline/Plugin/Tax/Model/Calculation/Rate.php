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
namespace Mageplaza\QuickbooksOnline\Plugin\Tax\Model\Calculation;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Model\Calculation\Rate as CoreRate;
use Mageplaza\QuickbooksOnline\Helper\Data as HelperData;
use Mageplaza\QuickbooksOnline\Helper\Sync as HelperSync;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;

/**
 * Class Rate
 * @package Mageplaza\QuickbooksOnline\Plugin\Tax\Model\Calculation
 */
class Rate
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
     * Rate constructor.
     *
     * @param HelperData $helperData
     * @param HelperSync $helperSync
     */
    public function __construct(
        HelperData $helperData,
        HelperSync $helperSync
    ) {
        $this->helperData = $helperData;
        $this->helperSync = $helperSync;
    }

    /**
     * @param CoreRate $subject
     * @param callable $proceed
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function aroundAfterSave(
        CoreRate $subject,
        callable $proceed
    ) {
        if (!$this->helperData->isEnabled()) {
            return $proceed();
        }

        if ($subject->isObjectNew() && !$subject->hasQueueSave()) {
            $this->helperSync->addObjectToQueue(QuickbooksModule::TAX, $subject);
        }

        return $proceed();
    }
}
