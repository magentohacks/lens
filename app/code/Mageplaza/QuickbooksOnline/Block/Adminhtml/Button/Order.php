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
namespace Mageplaza\QuickbooksOnline\Block\Adminhtml\Button;

/**
 * Class Order
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Button
 */
class Order extends AbstractButton
{
    /**
     * @inheritDoc
     */
    public function initButton()
    {
        $order = $this->getParentBlock()->getOrder();

        $orderId = $order->getId();
        if ($orderId && !$order->getQuickbooksEntity()) {
            $this->addButton($orderId);
        }
    }

    /**
     * @return string
     */
    public function getPathUrl()
    {
        return 'mpquickbooks/order/add';
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function getParamUrl($id)
    {
        return ['order_id' => $id];
    }
}
