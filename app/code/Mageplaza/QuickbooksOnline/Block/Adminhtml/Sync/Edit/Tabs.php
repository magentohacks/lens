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
namespace Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit;

use Exception;
use Magento\Backend\Block\Widget;
use Magento\Backend\Block\Widget\Tabs as CoreTabs;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class Tabs
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Sync\Edit
 */
class Tabs extends CoreTabs
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sync_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Synchronization Information'));
    }

    /**
     * @return Widget|AbstractBlock|void
     * @throws Exception
     */
    protected function _beforeToHtml()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $this->addTab('queue_report', 'mageplaza_quickbooks_sync_edit_tab_queue_report');
        }

        parent::_beforeToHtml();
    }
}
