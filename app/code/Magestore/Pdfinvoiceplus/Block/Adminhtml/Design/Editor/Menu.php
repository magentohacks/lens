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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\Design\Editor;

/**
 * class Menu
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class Menu extends AbstractBlock
{
    protected $_template = 'Magestore_Pdfinvoiceplus::editor/menu.phtml';

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl("pdfinvoiceplusadmin/index/edit", ["template_id" => $this->getPdfTemplateId()]);
    }

    /**
     * @return string
     */
    public function getAdvanceEditHtmlUrl()
    {
        return $this->getAjaxUrl('pdfinvoiceplusadmin/design/advanceEdit', [
            'design_type' => $this->getDesignType(),
            'template_id' => $this->getPdfTemplateId(),
        ]);
    }

    /**
     * @return string
     */
    public function getSyncInfoUpdateTemplateUrl()
    {
        return $this->getUrl('pdfinvoiceplusadmin/design_syncInformation/updateTemplate', [
                'template_id' => $this->getPdfTemplateId(),
                'design_type' => $this->getDesignType(),
            ]
        );
    }
}