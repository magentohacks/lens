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

namespace Magestore\Pdfinvoiceplus\Model\Variables\Option;

/**
 * class AbstractVariableConfig
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractVariableConfig implements \Magento\Framework\Option\ArrayInterface
{
    /**
     *
     */
    const XML_PATH_VARIABLES_CUSTOMER = 'pdfinvoiceplus/variable/customer_variables';
    /**
     *
     */
    const XML_PATH_VARIABLES_ORDER = 'pdfinvoiceplus/variable/order_variables';
    /**
     *
     */
    const XML_PATH_VARIABLES_ORDER_ITEM = 'pdfinvoiceplus/variable/order_items_variables';
    /**
     *
     */
    const XML_PATH_VARIABLES_INVOICE = 'pdfinvoiceplus/variable/invoice_variables';
    /**
     *
     */
    const XML_PATH_VARIABLES_INVOICE_ITEM = 'pdfinvoiceplus/variable/invoice_items_variables';
    /**
     *
     */
    const XML_PATH_VARIABLES_CREDITMEMO = 'pdfinvoiceplus/variable/creditmemo_variables';
    /**
     *
     */
    const XML_PATH_VARIABLES_CREDITMEMO_ITEM = 'pdfinvoiceplus/variable/creditmemo_items_variables';
    /**
     *
     */
    const XML_PATH_VARIABLES_SHIPMENT = 'pdfinvoiceplus/variable/shipment_variables';
    /**
     *
     */
    const XML_PATH_VARIABLES_SHIPMENT_ITEM = 'pdfinvoiceplus/variable/shipment_items_variables';
    /**
     *
     */
    const XML_PATH_VARIABLES_QUOTE = 'pdfinvoiceplus/variable/quote_variables';
    /**
     *
     */
    const XML_PATH_VARIABLES_QUOTE_ITEM = 'pdfinvoiceplus/variable/quote_items_variables';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magestore\Pdfinvoiceplus\Model\OptionManager
     */
    protected $_optionManager;

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter
     */
    protected $_optionConverter;

    /**
     * @var string
     */
    protected $_configVariablePath = '';

    /**
     * AbstractVariableConfig constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\Pdfinvoiceplus\Model\OptionManager $optionManager,
        \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $optionConverter
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_optionManager = $optionManager;
        $this->_optionConverter = $optionConverter;
    }

    /**
     * @return mixed
     */
    public function getVariablesConfig()
    {
        return unserialize($this->_scopeConfig->getValue($this->getConfigVariablesPath()));
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];
        if ($variablesConfig = $this->getVariablesConfig()) {
            $optionFlatArray = $this->_optionConverter->toFlatArray($this->getOptionVariableObject()->toOptionArray());
            foreach ($variablesConfig['options'] as $opt) {
                if (isset($optionFlatArray[$opt])) {
                    $options[] = ['label' => $optionFlatArray[$opt], 'value' => $opt];
                }
            }
        }

        return $options;
    }

    /**
     * Get config variables path
     *
     * @return string
     */
    abstract public function getConfigVariablesPath();

    /**
     * Get object config variable
     *
     * @return \Magento\Framework\Option\ArrayInterface
     */
    abstract public function getOptionVariableObject();
}