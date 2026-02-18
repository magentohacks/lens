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
 * Class AdvanceEdit
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
class AdvanceEdit extends AbstractBlock
{
    protected $_template = 'Magestore_Pdfinvoiceplus::advance-edit.phtml';

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl("pdfinvoiceplusadmin/design/edit", [
            "template_id" => $this->getPdfTemplateId(),
            "design_type" => $this->getDesignType(),
        ]);
    }

    /**
     * @return string
     */
    public function getSyncInfoResetTemplateUrl()
    {
        return $this->getUrl('pdfinvoiceplusadmin/design_syncInformation/resetTemplate', [
            'template_id' => $this->getPdfTemplateId(),
            'design_type' => $this->getDesignType(),
            'advance'     => true,
        ]);
    }

    /**
     * @return string
     */
    public function getSaveHtmlPostUrl($params = [])
    {
        return $this->getAjaxUrl('pdfinvoiceplusadmin/design/saveHtmlPost', $params);
    }
}