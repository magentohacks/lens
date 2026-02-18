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
namespace Mageplaza\QuickbooksOnline\Controller\Adminhtml\Customer;

use Magento\Customer\Model\Customer;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\QuickbooksOnline\Controller\Adminhtml\AbstractMassAction;

/**
 * Class Add
 * @package Mageplaza\QuickbooksOnline\Controller\Adminhtml\Customer
 */
class Add extends AbstractMassAction
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        return $this->addToQueue();
    }

    /**
     * @return Customer|mixed
     */
    public function getModel()
    {
        return $this->customerFactory->create();
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return 'customer/index/edit';
    }
}
