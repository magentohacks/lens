<?php

namespace BoostMyShop\Supplier\Block\Order\Edit\Tab;

class Misc extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_localeLists;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        array $data = []
    ) {

        parent::__construct($context, $registry, $formFactory, $data);

        $this->_localeLists = $localeLists;
    }

    /**
     * Prepare form fields
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var $model \Magento\User\Model\User */
        $model = $this->_coreRegistry->registry('current_purchase_order');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('po_');

        $baseFieldset = $form->addFieldset('misc_fieldset', ['legend' => __('Costs')]);

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);

        $baseFieldset->addField(
            'po_currency',
            'select',
            [
                'name' => 'po_currency',
                'label' => __('Currency'),
                'id' => 'po_currency',
                'title' => __('Currency'),
                'values' => $this->_localeLists->getOptionCurrencies(),
                'class' => 'select'
            ]
        );

        $baseFieldset->addField(
            'po_change_rate',
            'text',
            [
                'name' => 'po_change_rate',
                'label' => __('Change rate'),
                'id' => 'po_change_rate',
                'title' => __('Change rate')
            ]
        );

        $baseFieldset->addField(
            'po_shipping_cost',
            'text',
            [
                'name' => 'po_shipping_cost',
                'label' => __('Shipping cost'),
                'id' => 'po_shipping_cost',
                'title' => __('Shipping cost')
            ]
        );

        $baseFieldset->addField(
            'po_additionnal_cost',
            'text',
            [
                'name' => 'po_additionnal_cost',
                'label' => __('Additional cost'),
                'id' => 'po_additionnal_cost',
                'title' => __('Additional cost')
            ]
        );

        $baseFieldset->addField(
            'po_tax_rate',
            'text',
            [
                'name' => 'po_tax_rate',
                'label' => __('Tax rate %'),
                'id' => 'po_tax_rate',
                'title' => __('Tax rate %')
            ]
        );

        $shippingFieldset = $form->addFieldset('shipping_fieldset', ['legend' => __('Shipping')]);

        $shippingFieldset->addField(
            'po_shipping_method',
            'text',
            [
                'name' => 'po_shipping_method',
                'label' => __('Shipping method'),
                'id' => 'po_shipping_method',
                'title' => __('Shipping method')
            ]
        );

        $shippingFieldset->addField(
            'po_shipping_tracking',
            'text',
            [
                'name' => 'po_shipping_tracking',
                'label' => __('Tracking #'),
                'id' => 'po_shipping_tracking',
                'title' => __('Tracking #')
            ]
        );

        $accountingFieldset = $form->addFieldset('accounting_fieldset', ['legend' => __('Accounting')]);

        $accountingFieldset->addField(
            'po_supplier_reference',
            'text',
            [
                'name' => 'po_supplier_reference',
                'label' => __('Supplier PO #'),
                'id' => 'po_supplier_reference',
                'title' => __('Supplier PO #')
            ]
        );

        $accountingFieldset->addField(
            'po_invoice_reference',
            'text',
            [
                'name' => 'po_invoice_reference',
                'label' => __('Supplier Invoice #'),
                'id' => 'po_invoice_reference',
                'title' => __('Supplier Invoice #')
            ]
        );

        $accountingFieldset->addField(
            'po_invoice_date',
            'date',
            [
                'name' => 'po_invoice_date',
                'label' => __('Invoice date'),
                'id' => 'po_invoice_date',
                'date_format' => $dateFormat,
                'title' => __('Invoice date')
            ]
        );

        $accountingFieldset->addField(
            'po_invoice_status',
            'select',
            [
                'name' => 'po_invoice_status',
                'label' => __('Invoice Status'),
                'id' => 'po_invoice_status',
                'title' => __('Invoice Status'),
                'class' => 'input-select',
                'options' => ['missing' => __('Missing'), 'to_pay' => __('To pay'), 'paid' => __('Paid')]
            ]
        );

        $accountingFieldset->addField(
            'po_payment_date',
            'date',
            [
                'name' => 'po_payment_date',
                'label' => __('Payment date'),
                'id' => 'po_payment_date',
                'date_format' => $dateFormat,
                'title' => __('Payment date')
            ]
        );

        $data = $model->getData();
        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
