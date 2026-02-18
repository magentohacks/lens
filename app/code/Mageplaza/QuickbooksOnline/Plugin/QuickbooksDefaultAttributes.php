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
namespace Mageplaza\QuickbooksOnline\Plugin;

use Magento\Eav\Model\Entity\AbstractEntity;

/**
 * Class QuickbooksDefaultAttributes
 * @package Mageplaza\QuickbooksOnline\Plugin
 */
class QuickbooksDefaultAttributes
{
    /**
     * @param AbstractEntity $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings("Unused")
     */
    public function afterGetDefaultAttributes(AbstractEntity $subject, $result)
    {
        $quickbooksEntity = [
            'quickbooks_entity',
            'quickbooks_sync_token'
        ];

        return array_merge($result, $quickbooksEntity);
    }
}
