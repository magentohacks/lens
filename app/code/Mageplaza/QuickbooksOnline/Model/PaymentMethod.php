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
namespace Mageplaza\QuickbooksOnline\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class PaymentMethod
 * @package Mageplaza\QuickbooksOnline\Model
 */
class PaymentMethod extends AbstractModel
{
    const CACHE_TAG = 'mageplaza_quickbooks_payment_method';

    /**
     * @var string
     */
    protected $_cacheTag = 'mageplaza_quickbooks_payment_method';

    /**
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_quickbooks_payment_method';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\PaymentMethod::class);
    }
}
