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

namespace Magestore\Pdfinvoiceplus\Model\Variables;

/**
 * class AbstractVariableOption
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractVariableOption implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var string
     */
    const PREFIX_VAR_CUSTOMER = 'customer';
    /**
     * @var string
     */
    const PREFIX_VAR_ORDER = 'order';
    /**
     * @var string
     */
    const PREFIX_VAR_INVOICE = 'invoice';
    /**
     * @var string
     */
    const PREFIX_VAR_CREDITMEMO = 'creditmemo';
    /**
     * @var string
     */
    const PREFIX_VAR_SHIPMENT = 'shipment';
    /**
     * @var string
     */
    const PREFIX_VAR_QUOTE = 'quote';
    /**
     * @var string
     */
    const PREFIX_VAR_ITEMS = 'items';
    /**
     * @var string
     */
    protected $_prefixVar = '';
    /**
     * @var array
     */
    protected $_additional_vars = [];
    /**
     * @var string
     */
    protected $_tableVar = '';
    /**
     * @var \Magestore\Pdfinvoiceplus\Model\Variables\Resource\VariableList
     */
    protected $_resourceVariableList;

    /**
     * AbstractVariable constructor.
     *
     * @param \Magestore\Pdfinvoiceplus\Model\Variables\Resource\VariableList $resourceVariableList
     */
    public function __construct(\Magestore\Pdfinvoiceplus\Model\Variables\Resource\VariableList $resourceVariableList)
    {
        $this->_resourceVariableList = $resourceVariableList;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->_resourceVariableList->getVariableList($this->_tableVar) as $item) {
            $options[] = [
                'value' => "{{var " . $this->getPrefixVariable() . "_{$item['COLUMN_NAME']}}}",
                'label' => $item['COLUMN_COMMENT']
                    ? $item['COLUMN_COMMENT'] : __(ucwords(str_replace('_', ' ', $item['COLUMN_NAME'])))
            ];
        }

        //add additional vars
        foreach ($this->getAdditionalVar() as $key => $var) {
            $options[] = [
                'value' => "{{var " . $this->getPrefixVariable() . "_{$key}}}",
                'label' => $var
            ];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function getAdditionalVar()
    {
        return [
            'payment_method' => __('Payment Method'),
            'shipping_method' => __('Shipping Method'),
            'currency' => __('Currency'),
            'billing_address' => __('Billing Address'),
            'shipping_address' => __('Shipping Address')
        ];
    }

    /**
     * Get prefix variable
     *
     * @return string
     */
    abstract public function getPrefixVariable();
}