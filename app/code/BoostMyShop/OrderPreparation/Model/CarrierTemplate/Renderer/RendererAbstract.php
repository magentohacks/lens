<?php

namespace BoostMyShop\OrderPreparation\Model\CarrierTemplate\Renderer;

abstract class RendererAbstract
{
    abstract function getShippingLabelFile($ordersInProgress, $carrierTemplate);
}