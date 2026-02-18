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
namespace Mageplaza\QuickbooksOnline\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class QueueStatus
 * @package Mageplaza\QuickbooksOnline\Model\Source
 */
class QueueStatus implements OptionSourceInterface
{
    const PENDING = 1;
    const SUCCESS = 2;
    const ERROR   = 3;

    /**
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::PENDING => __('Pending'),
            self::SUCCESS => __('Success'),
            self::ERROR   => __('Error')
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];

        foreach ($this->getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
