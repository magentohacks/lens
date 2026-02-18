<?php
namespace Konstant\Managefaq\Block\Adminhtml\Faq\Edit\Tab;

use Konstant\Managefaq\Model\Status;

/**
 * FAQ Edit tab.
 * @category Konstant
 * @package  Konstant_Managefaq
 * @module   Managefaq
 * @author   Konstant Infosolutions Pvt. Ltd.
 */

class Faq extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_objectFactory;

    protected $_faq;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Konstant\Managefaq\Model\Faq $faq,
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_faq = $faq;
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
		$faqAttributes = $this->_faq->getStoreAttributes();
        $faqAttributesInStores = ['store_id' => ''];

        foreach ($faqAttributes as $faqAttribute) {
            $faqAttributesInStores[$faqAttribute.'_in_store'] = '';
        }

        $dataObj = $this->_objectFactory->create(
            ['data' => $faqAttributesInStores]
        );
        $model = $this->_coreRegistry->registry('faq');

        $dataObj->addData($model->getData());

        $storeViewId = $this->getRequest()->getParam('store');
		
        $form = $this->_formFactory->create();
		
        $form->setHtmlIdPrefix($this->_faq->getFormFieldHtmlIdPrefix());
		
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('FAQ Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $elements = [];
        $elements['question'] = $fieldset->addField(
            'question',
            'textarea',
            [
                'name' => 'question',
                'label' => __('Question'),
                'title' => __('Question'),
                'required' => true,
            ]
        );
		
		$elements['answer'] = $fieldset->addField(
            'answer',
            'textarea',
            [
                'name' => 'answer',
                'label' => __('Answer'),
                'title' => __('Answer'),
                'required' => true,
            ]
        );

		$elements['category_id'] = $fieldset->addField(
			'category_id',
			'select',
			[
				'label' => __('Category'),
				'name' => 'category_id',
				'values' => $model->getAvailableCategory(),
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

    public function getFaq()
    {
        return $this->_coreRegistry->registry('faq');
    }

    public function getPageTitle()
    {
        return $this->getFaq()->getId()? __("EDIT FAQ '%1'", $this->escapeHtml($this->getFaq()->getName())) : __('NEW FAQ');
    }

    public function getTabLabel()
    {
        return __('FAQ Information');
    }

    public function getTabTitle()
    {
        return __('FAQ Information');
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
