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

namespace Magestore\Pdfinvoiceplus\Model\PdfTemplateRender\TotalRender;

/**
 * Class Checkbox
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Order extends TotalsAbstractRender
{
    /**
     * @var string
     */
    protected $_varPrefix = 'order';

    /**
     * @param \Magento\Sales\Model\AbstractModel $entity
     * @param $templateHtml
     * @return mixed
     */
    public function render(\Magento\Sales\Model\AbstractModel $entity, $templateHtml)
    {
        $this->setRenderingEntity($entity);

        return $this->processTotalHtml($templateHtml);
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        $this->_registry->unregister('order');
        $this->_registry->register('order', $this->getOrder());
        $this->_registry->unregister('current_order');
        $this->_registry->register('current_order', $this->getOrder());

        /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\Totals\Order $orderTotals */
        $orderTotals = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Block\Adminhtml\Totals\Order');

        return $orderTotals->getTotals();
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getRenderingEntity();
    }
}