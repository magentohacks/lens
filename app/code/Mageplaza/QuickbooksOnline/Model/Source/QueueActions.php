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
 * Class QueueActions
 * @package Mageplaza\QuickbooksOnline\Model\Source
 */
class QueueActions implements OptionSourceInterface
{
    const CREATE = 1;
    const UPDATE = 2;
    const DELETE = 3;

    /**
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::CREATE => __('Create'),
            self::UPDATE => __('Update'),
            self::DELETE => __('Delete')
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
