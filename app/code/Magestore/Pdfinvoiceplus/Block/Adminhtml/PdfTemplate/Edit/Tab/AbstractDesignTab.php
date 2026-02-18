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

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\PdfTemplate\Edit\Tab;

/**
 * abstract class AbstractDesignTab
 *
 * @category Magestore
 * @package  Magestore_Pdfinvoiceplus
 * @module   Pdfinvoiceplus
 * @author   Magestore Developer
 */
abstract class AbstractDesignTab extends AbstractTab
{
    protected $_designLabel = '';

    const NOTE_FILE_NAME = 'To save an %s with custom name including: customer’s name, date, etc.';

    const NOTE_BARCODE = 'To choose information encoded in barcode on printed %s, including customer’s name, date, etc.';

    const NOTE_BROWSER = 'Note: Firefox or Google Chrome browser is recommended for the best performance of Design Editor.';

    /**
     * @return string
     */
    abstract protected function _getDesignType();

    /**
     * @return mixed
     */
    abstract public function getLoadVariablesUrl();

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getNoteFileName()
    {
        return __(sprintf(self::NOTE_FILE_NAME, $this->_designLabel));
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getNoteBarcode()
    {
        return __(sprintf(self::NOTE_BARCODE, $this->_designLabel));
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getNoteBrowser()
    {
        return __(self::NOTE_BROWSER);
    }

    /**
     * @param $target
     *
     * @return string
     */
    public function _getInsertVarButtonHtml($target)
    {
        $attributes = [
            'class'          => 'action-default scalable primary btn-insert-variable',
            'type'           => 'button',
            'data-mage-init' => $this->getDataMageInitInsertVariable($target),
        ];

        return $this->_buttonBuilder->build(__('Insert Variable...'), $attributes);
    }

    /**
     * @return string
     */
    public function getDataMageInitInsertVariable($target)
    {
        return $this->escapeQuote(\Laminas\Json\Json::encode(
            [
                'insertVariable' => [
                    'url'    => $this->getLoadVariablesUrl(),
                    'target' => $target,
                    'type'   => $this->_getDesignType(),
                ],
            ]
        ));
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function getEditDesignButtonHtml($data = [])
    {
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
        $model = $this->getRegistryModel();

        $data = array_merge([
            'design_type' => $this->_getDesignType(),
        ], $data);

        $attributes = [
            'class'          => 'action-default scalable primary btn-edit-design',
            'type'           => 'button',
            'data-mage-init' => $this->getDataMageInitEditDesign(),
        ];

        if ($model->getId()) {
            $data['template_id'] = $model->getId();
        } else {
            $attributes['disabled'] = 'disabled';
        }

        return $this->_buttonBuilder->build(__('Edit Design'), $attributes, $data);
    }

    /**
     * @return string
     */
    public function getDataMageInitEditDesign()
    {
        return $this->escapeQuote(\Laminas\Json\Json::encode(
            [
                'Magestore_Pdfinvoiceplus/js/form/edit-design' => [],
            ]
        ));
    }

    /**
     * @param array $data
     */
    public function getPreviewButtonHtml($data = [])
    {
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
        $model = $this->getRegistryModel();

        $data = array_merge([
            'design_type' => $this->_getDesignType(),
        ], $data);

        $attributes = [
            'class' => 'action-default scalable primary btn-preview-design',
            'type'  => 'button',
        ];

        if ($model->getId()) {
            $data['template_id'] = $model->getId();
            $attributes['data-mage-init'] = $this->getDataMageInitPreviewDesign();
        } else {
            $attributes['disabled'] = 'disabled';
        }

        return $this->_buttonBuilder->build(__('Preview Design'), $attributes, $data);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function getDataMageInitPreviewDesign()
    {
        return $this->escapeQuote(\Laminas\Json\Json::encode(
            [
                'previewDesign' => [
                    'url' => $this->getUrl('pdfinvoiceplusadmin/previewDesign/' . $this->_getDesignType()),
                ],
            ]
        ));
    }
}