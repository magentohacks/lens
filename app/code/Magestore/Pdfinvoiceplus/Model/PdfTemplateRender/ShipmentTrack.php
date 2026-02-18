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
 * class ShipmentTrack
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class ShipmentTrack extends \Magestore\Pdfinvoiceplus\Model\AbstractPdfTemplateRender
{

    /**
     * Render entity data to a html template
     *
     * @param \Magento\Sales\Model\AbstractModel $entity
     * @param                                    $templateHtml
     *
     * @return mixed
     */
    public function render(\Magento\Sales\Model\AbstractModel $entity, $templateHtml)
    {
        $this->setRenderingEntity($entity);
        $variables = $this->getVariables();

        return $this->_pdfHelper->mappingVariablesTemplate($templateHtml, $variables);
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_objectManager->create('\Magento\Sales\Model\Order')->load($this->getRenderingEntity()->getOrderId());
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        /** @var \Magento\Sales\Model\Order\Shipment\Track $track */
        $track = $this->getRenderingEntity();
        $variable = [
            'track_title' => $track->getTitle(),
            'track_track_number' => $track->getNumber()
        ];

        return $variable;
    }


}