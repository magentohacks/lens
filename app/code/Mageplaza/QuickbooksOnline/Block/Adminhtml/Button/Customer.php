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

use Magento\Customer\Controller\RegistryConstants;

/**
 * Class Customer
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Button
 */
class Customer extends AbstractButton
{
    /**
     * @return mixed
     */
    public function getModel()
    {
        $id = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);

        return $this->customerFactory->create()->load($id);
    }

    /**
     * @return string
     */
    public function getPathUrl()
    {
        return 'mpquickbooks/customer/add';
    }
}
