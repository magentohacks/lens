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

namespace Magestore\Pdfinvoiceplus\Model;

/**
 * class AbstractObjectManager
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractObjectManager
{
    /**
     * Map of types which are references to classes.
     *
     * @var array
     */
    protected $_typeMap = [];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * OptionManager constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $typeMap
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $typeMap = []
    )
    {
        $this->_objectManager = $objectManager;
        $this->mergeTypes($typeMap);
    }

    /**
     * Add or override object types.
     *
     * @param array $typeMap
     */
    protected function mergeTypes(array $typeMap)
    {
        foreach ($typeMap as $typeInfo) {
            if (isset($typeInfo['type']) && isset($typeInfo['class'])) {
                $this->_typeMap[$typeInfo['type']] = $typeInfo['class'];
            }
        }
    }

    /**
     * @param $type
     *
     * @return mixed
     */
    abstract public function get($type);
}