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
namespace Mageplaza\QuickbooksOnline\Block\Adminhtml\Render;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Payment
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Render
 */
class Payment extends AbstractElement
{
    /**
     * @return mixed
     */
    public function toHtml()
    {
        return $this->getData('queue_data');
    }
}
