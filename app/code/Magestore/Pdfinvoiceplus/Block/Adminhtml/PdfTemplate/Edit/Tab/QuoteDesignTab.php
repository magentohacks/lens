<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 16/3/2016
 * Time: 2:35 PM
 */

namespace Magestore\Pdfinvoiceplus\Block\Adminhtml\PdfTemplate\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magestore\Pdfinvoiceplus\Model\OptionManager;

class QuoteDesignTab extends AbstractDesignTab implements TabInterface
{

    const NOTE_QUOTE_FILE_NAME = 'To save an %s with custom name including: quote’s entity Id, date, etc.';

    const NOTE_QUOTE_BARCODE = 'To choose information encoded in barcode on printed %s, including quote’s entity Id, date, etc.';

    /**
     * @var string
     */
    protected $_designLabel = 'quote';

    /**
     * @return string
     */
    protected function _getDesignType()
    {
        return \Magestore\Pdfinvoiceplus\Model\PdfTemplate::DESIGN_TYPE_QUOTE;
    }

    /**
     * @return mixed
     */
    public function getLoadVariablesUrl()
    {
        return $this->getUrl('pdfinvoiceplusadmin/insertVariable/quote');
    }

    /**
     * @return array
     */
    public function getBarcodeFilenameVariables()
    {
        return [
            'label' => __('Quote'),
            'value' => $this->_optionManager->get(OptionManager::OPTION_VARIABLE_BARCODEFILENAME_QUOTE)->toOptionArray(),
        ];
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magestore\Pdfinvoiceplus\Model\PdfTemplate $model */
        $model = $this->getRegistryModel();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'quote_design_fieldset',
            [
                'legend' => __('Quote Design'),
            ]
        );

        $fieldset->addField(
            'quote_filename',
            'text',
            [
                'name' => 'quote_filename',
                'label' => __('Name to save PDF Quote'),
                'title' => __('Name to save PDF Quote'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'insert_var_quote_filename',
            'note',
            [
                'text' => $this->_getInsertVarButtonHtml('quote_filename'),
                'after_element_html' => '<br/>' . $this->_getQuoteFilenameNote(),
            ]
        );

        $fieldset->addField(
            'barcode_quote',
            'text',
            [
                'name' => 'barcode_quote',
                'label' => __('Information encoded in Barcode'),
                'title' => __('Information encoded in Barcode'),
                'required' => true,

            ]
        );

        $fieldset->addField(
            'insert_var_quote_barcode',
            'note',
            [
                'text' => $this->_getInsertVarButtonHtml('barcode_quote'),
                'after_element_html' => '<br/>' . $this->_getQuoteBarcodeNote(),
            ]
        );

        $fieldset->addField(
            'edit_design_quote',
            'note',
            [
                'text' => $this->getEditDesignButtonHtml() . ' ' . $this->getPreviewButtonHtml(),
                'after_element_html' => '<br/>' . $this->getNoteBrowser(),
            ]
        );

        if (!$model->getId()) {
            $model->addData([
                'quote_filename' => 'quote_{{var quote_entity_id}}_{{var quote_created_at}}',
                'barcode_quote' => '{{var quote_entity_id}}',
            ]);
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Return Tab label.
     *
     * @return string
     *
     * @api
     */
    public function getTabLabel()
    {
        return __('Quote Design');
    }

    /**
     * Return Tab title.
     *
     * @return string
     *
     * @api
     */
    public function getTabTitle()
    {
        return __('Quote Design');
    }

    /**
     * Can show tab in tabs.
     *
     * @return bool
     *
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden.
     *
     * @return bool
     *
     * @api
     */
    public function isHidden()
    {
        return false;
    }

    protected function _getQuoteFilenameNote()
    {
        return __(sprintf(self::NOTE_QUOTE_FILE_NAME, $this->_designLabel));
    }

    protected function _getQuoteBarcodeNote()
    {
        return __(sprintf(self::NOTE_QUOTE_BARCODE, $this->_designLabel));
    }
}