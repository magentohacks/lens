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
 * class AbstractVariableItemOption
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractVariableItemOption extends AbstractVariableOption
{
    /**
     * Get prefix variable
     *
     * @return string
     */
    public function getPrefixVariable()
    {
        return AbstractVariableOption::PREFIX_VAR_ITEMS;
    }

    /**
     * @return array
     */
    public function getAdditionalVar()
    {
        return [
            'small_image' => __('Product Image')
        ];
    }

}