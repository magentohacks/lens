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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\TemplateBuilder;

/**
 * class ShipmentTrack
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class ShipmentTrack extends \Magestore\Pdfinvoiceplus\Block\Adminhtml\AbstractTemplateInformation
{
    /**
     * @var string
     */
    const COLUMN_TITLE = '{{var track_title}}';
    /**
     * @var string
     */
    const COLUMN_NUMBER = '{{var track_track_number}}';

    protected $_template = 'Magestore_Pdfinvoiceplus::default-template/track-item.phtml';

    /**
     * @return array
     */
    public function getItemMap()
    {
        return [
            static::COLUMN_TITLE    => strtoupper($this->translate('Title')),
            static::COLUMN_NUMBER   => strtoupper($this->translate('Number'))
        ];
    }
}