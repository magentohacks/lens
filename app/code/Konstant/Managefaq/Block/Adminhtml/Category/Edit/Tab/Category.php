<?php
namespace Konstant\Managefaq\Block\Adminhtml\Category\Edit\Tab;

use Konstant\Managefaq\Model\Status;

/**
 * Category Edit tab.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Category extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_objectFactory;
	protected $_category;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Konstant\Managefaq\Model\Category $category,
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_category = $category;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareLayout()
    {
		
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());

        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
             $this->getLayout()->createBlock(
                'Konstant\Managefaq\Block\Adminhtml\Form\Renderer\Fieldset\Element',
                $this->getNameInLayout().'_fieldset_element'
            ) 
        );

        return $this;
    }

    protected function _prepareForm()
    {
		$categoryAttributes = $this->_category->getStoreAttributes();
        $categoryAttributesInStores = ['store_id' => ''];
		
        foreach ($categoryAttributes as $categoryAttribute) {
            $categoryAttributesInStores[$categoryAttribute.'_in_store'] = '';
        }

        $dataObj = $this->_objectFactory->create(
            ['data' => $categoryAttributesInStores]
        );
        $model = $this->_coreRegistry->registry('category');

        $dataObj->addData($model->getData());

        $storeViewId = $this->getRequest()->getParam('store');
		
        $form = $this->_formFactory->create();
		
        $form->setHtmlIdPrefix($this->_category->getFormFieldHtmlIdPrefix());
		
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Category Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $elements = [];
        $elements['title'] = $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
            ]
        );
		
        $elements['status'] = $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'options' => Status::getAvailableStatuses(),
            ]
        );

        $form->addValues($dataObj->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getCategory()
    {
        return $this->_coreRegistry->registry('category');
    }

    public function getPageTitle()
    {
        return $this->getCategory()->getId()? __("Edit Category '%1'", $this->escapeHtml($this->getCategory()->getName())) : __('New Category');
    }

    public function getTabLabel()
    {
        return __('Category Information');
    }

    public function getTabTitle()
    {
        return __('Category Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
