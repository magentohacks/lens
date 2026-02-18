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
 * Class Creditmemo
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Creditmemo extends TotalsAbstractRender
{
    /**
     * @var string
     */
    protected $_varPrefix = 'creditmemo';

    /**
     * Render entity data to a html template
     *
     * @param \Magento\Sales\Model\AbstractModel $entity
     * @param                            $templateHtml
     *
     * @return string
     */
    public function render(\Magento\Sales\Model\AbstractModel $entity, $templateHtml)
    {
        $this->setRenderingEntity($entity);

        return $this->processTotalHtml($templateHtml);
    }

    /**
     * get total information
     * @return mixed
     */
    public function getTotals()
    {
        $this->_registry->unregister('creditmemo');
        $this->_registry->register('creditmemo', $this->getCreditmemo());
        $this->_registry->unregister('current_creditmemo');
        $this->_registry->register('current_creditmemo', $this->getCreditmemo());
        $this->_registry->unregister('current_order');
        $this->_registry->register('current_order', $this->getOrder());

        /** @var \Magestore\Pdfinvoiceplus\Block\Adminhtml\Totals\Creditmemo $creditmemoTotals */
        $creditmemoTotals = $this->_objectManager->create('Magestore\Pdfinvoiceplus\Block\Adminhtml\Totals\Creditmemo');

        return $creditmemoTotals->getTotals();
    }

    /**
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->getRenderingEntity();
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getCreditmemo()->getOrder();
    }
}