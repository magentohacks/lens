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
namespace Mageplaza\QuickbooksOnline\Model\ResourceModel\Sync\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mageplaza\QuickbooksOnline\Model\Source\QueueStatus;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package Mageplaza\QuickbooksOnline\Model\ResourceModel\Sync\Grid
 */
class Collection extends SearchResult
{
    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(
            [
                'main_table' => $this->getMainTable()
            ]
        );

        $this->getSelect()->columns(
            [
                'total_object'  => $this->getChildSql(),
                'total_pending' => $this->getChildSql(QueueStatus::PENDING),
                'total_request' => $this->getChildSql([QueueStatus::SUCCESS, QueueStatus::ERROR])
            ]
        );

        return $this;
    }

    /**
     * @param bool $status
     *
     * @return Zend_Db_Expr
     */
    public function getChildSql($status = false)
    {
        $child = clone $this->getSelect();
        $child->reset();
        $child->from(['queue' => $this->getTable('mageplaza_quickbooks_queue')], 'COUNT(*)');
        $child->where('queue.sync_id = main_table.sync_id');

        if ($status) {
            if (is_array($status)) {
                $child->where('queue.status =' . $status[0] . ' OR ' . 'queue.status =' . $status[1]);
            } else {
                $child->where('queue.status =' . $status);
            }
        }

        return new Zend_Db_Expr('(' . $child->__toString() . ')');
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return mixed
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'website_ids') {
            return parent::addFieldToFilter('website_ids', ['finset' => $condition['eq']]);
        }
    }
}
