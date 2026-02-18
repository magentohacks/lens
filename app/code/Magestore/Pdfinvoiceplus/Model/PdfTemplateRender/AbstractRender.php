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
 * class AbstractRender
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractRender extends \Magestore\Pdfinvoiceplus\Model\AbstractPdfTemplateRender
{
    protected $_itemRenderer;

    protected $_type = '';

    /**
     * Render entity data to a html template
     *
     * @param \Magento\Sales\Model\AbstractModel $entity
     * @param                            $templateHtml
     *
     * @return mixed
     */
    public function render(\Magento\Sales\Model\AbstractModel $entity, $templateHtml)
    {
        parent::render($entity, $templateHtml);
        if (!$entity->getId()) {
            return $this->getProcessedHtml();
        }

        $this->_processItemsHtml();
        $this->_processTotalHtml();

        $variables = $this->getVariables();

        $processedHtml = $this->getProcessedHtml();
        $processedHtml = $this->_processAddressHtml($processedHtml, $variables, 'shipping_address');
        $processedHtml = $this->_processAddressHtml($processedHtml, $variables, 'billing_address');
        $processedHtml = $this->_pdfHelper->mappingVariablesTemplate($processedHtml, $variables);
        $processedHtml = preg_replace('#\{\*.*\*\}#suU', '', $processedHtml);

        $this->setProcessedHtml($processedHtml);

        return $this->getProcessedHtml();
    }

    /**
     * @param $processedHtml
     * @param $variables
     * @param $addressName
     *
     * @return mixed
     */
    protected function _processAddressHtml($processedHtml, &$variables, $addressName)
    {
        $index = $this->getType() . '_' . $addressName;
        if (isset($variables[$index])) {
            $processedHtml = str_replace(
                '{{var ' . $index . '}}',
                $variables[$index],
                $processedHtml
            );
            unset($variables[$index]);
        }

        return $processedHtml;
    }

    /**
     * @param $srcHtml
     * @param $start
     * @param $end
     *
     * @return string
     */
    public function getTemplatePartForItems($srcHtml, $start, $end)
    {
        $txt = explode($start, $srcHtml);
        $txt2 = explode($end, $txt[1]);
        $explode = explode('<tbody>', $srcHtml);
        $explode2 = explode('</tbody>', $explode[1]);

        return $txt2[0] ? trim($txt2[0]) : trim($explode2[0]);
    }

    /**
     * @param string $templateHtml
     *
     * @return $this
     */
    protected function _processTotalHtml()
    {
        $templateHtml = $this->getTotalRenderer()->render($this->getRenderingEntity(), $this->getProcessedHtml());

        $this->setProcessedHtml($templateHtml);

        return $this;
    }

    /**
     * retrieve template with order items
     * @return $this
     */
    protected function _processItemsHtml()
    {
        $itemsTemplateFilled = '';
        $itemsTemplate = $this->getTemplatePartForItems($this->getProcessedHtml(), self::THE_START, self::THE_END);

        foreach ($this->getRenderingEntity()->getAllItems() as $item) {
            $itemsTemplateFilled .= $this->getItemRenderer()->render($item, $itemsTemplate) . '<br>';
        }

        $templateHtml = str_replace($itemsTemplate, $itemsTemplateFilled, $this->getProcessedHtml());
        $this->setProcessedHtml($templateHtml);

        return $this;
    }

    /**
     * @param mixed $itemRenderer
     *
     * @return AbstractRender
     */
    public function setItemRenderer($itemRenderer)
    {
        $this->_itemRenderer = $itemRenderer;

        return $this;
    }

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface
     */
    abstract public function getTotalRenderer();

    /**
     * @return \Magestore\Pdfinvoiceplus\Model\PdfTemplateRenderInterface
     */
    abstract public function getItemRenderer();

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
}