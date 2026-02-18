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

namespace Magestore\Pdfinvoiceplus\Block\Widget;

/**
 * class ButtonBuilder
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class ButtonBuilder
{
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * ButtonBuilder constructor.
     *
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(\Magento\Framework\Escaper $escaper)
    {
        $this->_escaper = $escaper;
    }

    /**
     * @param       $label
     * @param array $attributes
     * @param array $dataJson
     *
     * @return string
     */
    public function build($label, $attributes = [], $data = [])
    {
        $buttonHtml = '<button';

        if (!isset($attributes['title']) || empty($attributes['title'])) {
            $attributes['title'] = $label;
        }

        if (!empty($data)) {
            $attributes['data-button-data'] = $this->_escaper->escapeQuote(\Laminas\Json\Json::encode($data));
        }

        foreach ($attributes as $attributeName => $value) {
            if (is_string($value)) {
                $buttonHtml .= sprintf(' %s="%s"', $attributeName, $value);
            }
        }

        $buttonHtml .= '>' . $label . '</button>';

        return $buttonHtml;
    }
}