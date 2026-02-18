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
 * class CreditmemoItem
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class CreditmemoItem extends \Magestore\Pdfinvoiceplus\Model\Variables\AbstractVariableItemOption
{
    /**
     * @var string
     */
    protected $_tableVar = 'sales_creditmemo_item';

    protected $_prefixVar = 'creditmemo';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $options[] = [
            'value' => "{{var " . $this->_prefixVar . "_tax_percent}}",
            'label' => 'Tax Percent'
        ];

        return $options;
    }
}