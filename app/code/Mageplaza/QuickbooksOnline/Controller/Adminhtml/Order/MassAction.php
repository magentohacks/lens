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
namespace Mageplaza\QuickbooksOnline\Controller\Adminhtml\Order;

use Magento\Sales\Model\ResourceModel\Order\Collection;
use Mageplaza\QuickbooksOnline\Controller\Adminhtml\AbstractMassAction;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;

/**
 * Class MassAction
 * @package Mageplaza\QuickbooksOnline\Controller\Adminhtml\Order
 */
class MassAction extends AbstractMassAction
{
    /**
     * @return string
     */
    public function getType()
    {
        return QuickbooksModule::ORDER;
    }

    /**
     * @return Collection|mixed
     */
    public function getCollection()
    {
        return $this->orderCollectionFactory->create();
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return 'sales/order/';
    }
}
