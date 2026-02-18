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

namespace Magestore\Pdfinvoiceplus\Model\Variables\Option\Config;

use Magestore\Pdfinvoiceplus\Model\OptionManager;

/**
 * class QuoteItem
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class QuoteItem extends \Magestore\Pdfinvoiceplus\Model\Variables\Option\AbstractVariableConfig
{

    /**
     * Get config variables path
     *
     * @return string
     */
    public function getConfigVariablesPath()
    {
        return self::XML_PATH_VARIABLES_QUOTE_ITEM;
    }

    /**
     * Get object config variable
     *
     * @return \Magento\Framework\Option\ArrayInterface
     */
    public function getOptionVariableObject()
    {
        return $this->_optionManager->get(OptionManager::OPTION_VARIABLE_QUOTE_ITEM);
    }
}