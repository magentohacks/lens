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
 * Class QuickbooksModule
 * @package Mageplaza\QuickbooksOnline\Model\Source
 */
class QuickbooksModule implements OptionSourceInterface
{
    const CUSTOMER       = 'customer';
    const PRODUCT        = 'item';
    const ORDER          = 'salesReceipt';
    const INVOICE        = 'invoice';
    const CREDIT_MEMO    = 'creditMemo';
    const PAYMENT_METHOD = 'paymentMethod';
    const TAX            = 'taxService';

    /**
     * @return array
     */
    public function getOptionArray()
    {
        return [
            self::CUSTOMER       => __('Customer'),
            self::PRODUCT        => __('Products and Services'),
            self::ORDER          => __('Sales Receipt'),
            self::INVOICE        => __('Invoice'),
            self::CREDIT_MEMO    => __('Credit Memo'),
            self::PAYMENT_METHOD => __('Payment Method'),
            self::TAX            => __('Tax Service'),
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
