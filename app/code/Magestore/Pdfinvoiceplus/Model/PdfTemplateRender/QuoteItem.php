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

namespace Magestore\Pdfinvoiceplus\Model\PdfTemplateRender;

/**
 * class QuoteItem
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 * @method  \Magento\Quote\Model\Quote\Item getRenderingEntity()
 */
class QuoteItem extends AbstractItemRender
{

    /**
     * @param \Magento\Quote\Model\Quote\Item $entity
     * @param $templateHtml
     * @return mixed
     */
    public function renderQuoteItem(\Magento\Quote\Model\Quote\Item $entity, $templateHtml)
    {
        $this->setRenderingEntity($entity);
        $variables = $this->getVariables();

        if (isset($variables['items_product_options'])) {
            $templateHtml = str_replace('{{var items_product_options}}', $variables['items_product_options'], $templateHtml);
            unset($variables['items_product_options']);
        }

        return $this->_pdfHelper->mappingVariablesTemplate($templateHtml, $variables);
    }

    public function getVariables()
    {
        $imageData = $this->getProductImage($this->getRenderingEntity()->getProduct()->getId());

        $itemsPriceData = $this->getItemsPriceData();

        $standardVars = $this->getStandardItemVars();
        $productOptions = $this->getItemOptions();

        $itemData = isset($productOptions)
            ? array_merge($itemsPriceData, $standardVars, $productOptions, $imageData)
            : array_merge($itemsPriceData, $standardVars, $imageData);

        return $this->_prepareVariablesData($itemData);
    }


    public function getStandardItemVars()
    {
        return [
            'items_name' => [
                'value' => $this->getRenderingEntity()->getName(),
                'label' => 'Product Name'
            ],
            'items_sku' => [
                'value' => $this->getSku(),
                'label' => 'SKU'
            ],
            'items_qty' => [
                'value' => $this->getQty(),
                'label' => 'Qty'
            ],
            'items_discount_amount' => [
                'value' => $this->getDiscountAmount(),
                'label' => 'Discount Amount'
            ]
        ];
    }

    public function getItemOptions()
    {
        $productOptionsLabeled = [];

        if ($options = $this->getRenderingEntity()->getProductOption()) {
            $result = [];
            if ($options) {
                if (isset($options['options'])) {
                    $result = array_merge($result, $options['options']);
                }
                if (isset($options['additional_options'])) {
                    $result = array_merge($result, $options['additional_options']);
                }
                if (isset($options['attributes_info'])) {
                    $result = array_merge($result, $options['attributes_info']);
                }
            }
            /* Will be able to split in three */
            $data = null;

            foreach ($result as $option => $value) {
                if ($value['label'] && $value['value']) {
                    $data .= $value['label'] . ' - ' . $value['value'] . '<br/>';
                }
            }

            $productOptionsLabeled = [
                'items_product_options' => [
                    'value' => $data,
                    'label' => __('Product options'),
                ],
            ];
        }

        return $productOptionsLabeled;
    }

    /**
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getOrderItem()
    {

    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_objectManager->create('Magento\Sales\Model\Order');
    }
}