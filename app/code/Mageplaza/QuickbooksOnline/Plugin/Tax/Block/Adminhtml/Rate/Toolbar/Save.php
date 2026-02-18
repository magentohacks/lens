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
namespace Mageplaza\QuickbooksOnline\Plugin\Tax\Block\Adminhtml\Rate\Toolbar;

use Magento\Tax\Block\Adminhtml\Rate\Toolbar\Save as CoreSave;
use Mageplaza\QuickbooksOnline\Helper\Data as HelperData;

/**
 * Class Save
 * @package Mageplaza\QuickbooksOnline\Plugin\Tax\Block\Adminhtml\Rate\Toolbar
 */
class Save
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Save constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param CoreSave $object
     */
    public function beforeSetLayout(CoreSave $object)
    {
        if ($this->helperData->isEnabled()) {
            $message = 'Are you sure you want to do this?';
            $rate    = (int) $object->getRequest()->getParam('rate');
            $url     = $object->getUrl('mpquickbooks/tax/add', ['rate' => $rate]);

            $object->addButton(
                'add_to_queue',
                [
                    'label'   => __('Add To Quickbooks Queue'),
                    'class'   => 'add_to_quickbooks',
                    'onclick' => "confirmSetLocation('{$message}', '{$url}')"
                ]
            );
        }
    }
}
