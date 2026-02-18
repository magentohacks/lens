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
namespace Mageplaza\QuickbooksOnline\Model\ResourceModel\Sync;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageplaza\QuickbooksOnline\Model\ResourceModel\Sync as ResourceSync;
use Mageplaza\QuickbooksOnline\Model\Sync;

/**
 * Class Collection
 * @package Mageplaza\QuickbooksOnline\Model\ResourceModel\Sync
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'sync_id';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(Sync::class, ResourceSync::class);
    }
}
