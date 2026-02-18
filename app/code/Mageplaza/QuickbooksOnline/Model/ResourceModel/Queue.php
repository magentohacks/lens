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
namespace Mageplaza\QuickbooksOnline\Model\ResourceModel;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mageplaza\QuickbooksOnline\Model\Source\QueueStatus;
use Mageplaza\QuickbooksOnline\Model\Source\QuickbooksModule;

/**
 * Class Queue
 * @package Mageplaza\QuickbooksOnline\Model\ResourceModel
 */
class Queue extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('mageplaza_quickbooks_queue', 'queue_id');
    }

    /**
     * @param array $data
     *
     * @throws Exception
     */
    public function insertQueues($data)
    {
        $this->getConnection()->beginTransaction();

        try {
            $tableName = $this->getMainTable();
            $this->getConnection()->insertMultiple($tableName, $data);
            $this->getConnection()->commit();
        } catch (Exception $e) {
            $this->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * @param array $queues
     *
     * @return $this
     * @throws Exception
     */
    public function updateQueues($queues)
    {
        $connection = $this->getConnection();
        $connection->beginTransaction();

        try {
            $connection->insertOnDuplicate(
                $this->getTable('mageplaza_quickbooks_queue'),
                $queues,
                ['queue_id', 'status', 'json_response', 'total_sync']
            );
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * @param array $quickbooksEntity
     * @param string $quickbooksFieldName
     *
     * @return $this
     * @throws Exception
     */
    public function updateQuickbooksEntity($quickbooksEntity, $quickbooksFieldName = '')
    {
        foreach ($quickbooksEntity as $quickbooksModule => $updateData) {
            $fieldName = 'entity_id';
            $tableName = '';

            switch ($quickbooksModule) {
                case QuickbooksModule::PRODUCT:
                    $tableName = 'catalog_product_entity';
                    break;
                case QuickbooksModule::CUSTOMER:
                    $tableName = 'customer_entity';
                    break;
                case QuickbooksModule::ORDER:
                    $tableName = 'sales_order';
                    break;
                case QuickbooksModule::INVOICE:
                    $tableName = 'sales_invoice';
                    break;
                case QuickbooksModule::CREDIT_MEMO:
                    $tableName = 'sales_creditmemo';
                    break;
                case QuickbooksModule::PAYMENT_METHOD:
                    $fieldName = 'method_id';
                    $tableName = 'mageplaza_quickbooks_payment_method';
                    break;
                case QuickbooksModule::TAX:
                    $fieldName = 'tax_calculation_rate_id';
                    $tableName = 'tax_calculation_rate';
                    break;
            }

            if (!$tableName) {
                return $this;
            }

            $connection = $this->getConnection();
            $value      = $connection->getCaseSql($fieldName, $updateData, $quickbooksFieldName);
            $where      = [$fieldName . ' IN (?)' => array_keys($updateData)];

            try {
                $connection->beginTransaction();
                $connection->update($this->getTable($tableName), [$quickbooksFieldName => $value], $where);
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollBack();
                throw $e;
            }
        }

        return $this;
    }

    /**
     * @param array $data
     */
    public function updatePaymentTitle($data)
    {
        $connection = $this->getConnection();

        try {
            foreach ($data as $methodId => $title) {
                $connection->update(
                    $this->getTable('mageplaza_quickbooks_payment_method'),
                    ['title' => $title],
                    'method_id = ' . $methodId
                );
            }
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
            $connection->rollBack();
        }
    }

    /**
     * @param string $days
     *
     * @throws LocalizedException
     */
    public function deleteRecordAfter($days)
    {
        if ($days) {
            $connection = $this->getConnection();
            $table      = $this->getMainTable();
            $statusSql  = 'status = ' . QueueStatus::SUCCESS;
            $connection->delete(
                $table,
                [
                    $statusSql,
                    'created_at < NOW() - INTERVAL ' . $days . ' DAY'
                ]
            );
        }
    }
}
