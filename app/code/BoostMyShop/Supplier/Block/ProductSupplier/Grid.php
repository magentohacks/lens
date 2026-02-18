<?php

namespace BoostMyShop\Supplier\Block\ProductSupplier;

use Magento\Backend\Block\Widget\Grid\Column;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_supplierCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \BoostMyShop\Supplier\Model\ResourceModel\ProductSupplier\AllFactory $productSupplierCollectionFactory,
        \BoostMyShop\Supplier\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);

        $this->_productSupplierCollectionFactory = $productSupplierCollectionFactory;
        $this->_supplierCollectionFactory = $supplierCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productSupplierGrid');
        $this->setTitle(__('Product / Supplier Association'));
        //$this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }


    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_productSupplierCollectionFactory->create();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('sku', ['header' => __('Sku'), 'index' => 'sku', 'renderer' => '\BoostMyShop\Supplier\Block\ProductSupplier\Renderer\Sku']);
        $this->addColumn('name', ['header' => __('Product'), 'index' => 'name']);
        $this->addColumn('supplier', ['header' => __('Supplier'), 'index' => 'sup_id', 'type' => 'options', 'options' => $this->getSupplierOptions()]);
        $this->addColumn('associated', ['header' => __('Associated'), 'align' => 'center', 'sortable' => false, 'index' => 'associated', 'type' => 'options', 'options' => ['' => ' ', 0 => __('No'), 1 => __('Yes')]]);
        $this->addColumn('sp_sku', ['header' => __('Supplier sku'), 'align' => 'center', 'index' => 'sp_sku', 'renderer' => '\BoostMyShop\Supplier\Block\ProductSupplier\Renderer\SupplierSku']);
        $this->addColumn('sp_price', ['header' => __('Buying price'), 'align' => 'center', 'index' => 'sp_price', 'renderer' => '\BoostMyShop\Supplier\Block\ProductSupplier\Renderer\Price']);
        $this->addColumn('sp_primary', ['header' => __('Is Primary'), 'align' => 'center', 'index' => 'sp_primary', 'type' => 'options', 'options' => ['' => ' ', 0 => __('No'), 1 => __('Yes')], 'renderer' => '\BoostMyShop\Supplier\Block\ProductSupplier\Renderer\Primary']);

        $this->_eventManager->dispatch('bms_supplier_productsupplier_grid_preparecolumns', ['grid' => $this]);

        //$this->addColumn('action', ['header' => __('Action'), 'align' => 'center', 'filter' => false, 'renderer' => '\BoostMyShop\Supplier\Block\ProductSupplier\Renderer\Action']);

        $this->addExportType('*/*/ExportCsv', __('CSV'));


        return parent::_prepareColumns();
    }


    public function getSupplierOptions()
    {
        $options = [];
        $options[''] = ' ';
        foreach($this->_supplierCollectionFactory->create()->setOrder('sup_name', 'asc') as $item)
        {
            $options[$item->getId()] = $item->getsup_name();
        }
        return $options;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('fake_id');

        $this->getMassactionBlock()->addItem(
            'remove',
            [
                'label' => __('Remove'),
                'url' => $this->getUrl(
                    '*/*/massRemoveProducts'
                ),
                'confirm' => __('Are you sure?')
            ]
        );

        $this->getMassactionBlock()->addItem(
            'associate',
            [
                'label' => __('Associate to supplier'),
                'url' => $this->getUrl(
                    '*/*/MassAssociateProducts'
                )
            ]
        );

        $modes = [];
        $modes[] = ['label' => 'Set stock to 0', 'value' => 'stock_to_0'];
        $modes[] = ['label' => 'Set stock to 999', 'value' => 'stock_to_999'];
        $modes[] = ['label' => 'Remove primary', 'value' => 'remove_primary'];
        $modes[] = ['label' => 'Remove supplier sku', 'value' => 'remove_sku'];
        $modes[] = ['label' => 'Remove buying price', 'value' => 'remove_price'];
        $this->getMassactionBlock()->addItem(
            'edit',
            [
                'label' => __('Mass edit'),
                'url' => $this->getUrl(
                    '*/*/MassEdit'
                ),
                'additional' => [
                    'mode' => [
                        'name' => 'mode',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Action'),
                        'values' => $modes,
                    ],
                ]

            ]
        );
    }

    public function getRowClass($row)
    {
        return 'product_supplier_'.$row->getfake_id();
    }

    protected function _prepareMassactionColumn()
    {
        $columnId = 'massaction';
        $massactionColumn = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Grid\Column')
            ->setData(
                [
                    'index' => $this->getMassactionIdField(),
                    'filter_index' => $this->getMassactionIdFilter(),
                    'type' => 'massaction',
                    'name' => $this->getMassactionBlock()->getFormFieldName(),
                    'is_system' => true,
                    'header_css_class' => 'col-select',
                    'column_css_class' => 'col-select',
                    'use_index' => 1,   //this is the code line that allows to get a custom ID used for massaction checkboxes
                ]
            );

        if ($this->getNoFilterMassactionColumn()) {
            $massactionColumn->setData('filter', false);
        }

        $massactionColumn->setSelected($this->getMassactionBlock()->getSelected())->setGrid($this)->setId($columnId);

        $this->getColumnSet()->insert(
            $massactionColumn,
            count($this->getColumnSet()->getColumns()) + 1,
            false,
            $columnId
        );
        return $this;
    }

}
