<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Pdfinvoiceplus\Model\Variables\Resource;

use \Magento\Framework\Config\File\ConfigFilePool;

/**
 * class VariableList
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class VariableList extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\App\DeploymentConfig\Reader
     */
    protected $_configReader;

    /**
     * VariableList constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param ConfigFilePool $configFilePool
     * @param \Magento\Framework\App\DeploymentConfig\Reader $configReader
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Config\File\ConfigFilePool $configFilePool,
        \Magento\Framework\App\DeploymentConfig\Reader $configReader,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->_configReader = $configReader;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        // TODO: Implement _construct() method.
    }

    /**
     * get column name/column comment from table
     * @return array
     */
    public function getVariableList($tableName = null)
    {

        if (isset($tableName)) {
            $connection = $this->getConnection();

            $table = $this->getTable($tableName);
            $select = $connection->select()->from(
                ['column' => 'INFORMATION_SCHEMA.COLUMNS'],
                [
                    'COLUMN_NAME',
                    'COLUMN_COMMENT'
                ]
            )->where('TABLE_NAME = :TABLE_NAME')->where('COLUMN_NAME NOT LIKE ? :BASE');

            $bind = [':TABLE_NAME' => $table, ':BASE' => '%base_%'];

            $configEnv = $this->_configReader->load(ConfigFilePool::APP_ENV);
            if (isset($configEnv['db']['connection']['default']['dbname'])) {
                $select->where('TABLE_SCHEMA = :TABLE_SCHEMA');
                $bind[':TABLE_SCHEMA'] = $configEnv['db']['connection']['default']['dbname'];
            }

            return $connection->fetchAll($select, $bind);
        }
        return [];
    }
}