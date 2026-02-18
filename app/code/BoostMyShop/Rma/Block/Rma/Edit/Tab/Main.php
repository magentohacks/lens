<?php

namespace BoostMyShop\Rma\Block\Rma\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected $_statusList;
    protected $_userList = null;

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
        \BoostMyShop\Rma\Model\Rma\Status $statusList,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userList,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->_statusList = $statusList;
        $this->_userList = $userList;
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
        $rma = $this->_coreRegistry->registry('current_rma');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rma_');

        $baseFieldset = $form->addFieldset('base_fieldset', ['legend' => __('Main')]);

        if ($rma->getId()) {
            $baseFieldset->addField('rma_id', 'hidden', ['name' => 'rma_id']);
        }

        $baseFieldset->addField(
            'rma_reference',
            'text',
            [
                'name' => 'rma_reference',
                'label' => __('Reference'),
                'id' => 'rma_reference',
                'title' => __('Reference'),
                'required' => true
            ]
        );

        $baseFieldset->addField(
            'rma_customer_name',
            'text',
            [
                'name' => 'rma_customer_name',
                'label' => __('Customer name'),
                'id' => 'rma_customer_name',
                'title' => __('Customer name'),
                'required' => true
            ]
        );

        if ($rma->getCustomer()) {
            $baseFieldset->addField(
                'rma_customer_account',
                'link',
                [
                    'name' => 'rma_customer_account',
                    'label' => __('Customer account'),
                    'id' => 'rma_customer_account',
                    'title' => __('Customer account'),
                    'href' => $this->getUrl('customer/index/edit', ['id' => $rma->getCustomer()->getId()])
                ]
            );
        }

        $baseFieldset->addField(
            'rma_customer_email',
            'text',
            [
                'name' => 'rma_customer_email',
                'label' => __('Customer email'),
                'id' => 'rma_customer_email',
                'title' => __('Customer email'),
                'required' => true
            ]
        );

        if ($rma->getOrder())
        {
            $baseFieldset->addField(
                'rma_order',
                'link',
                [
                    'name' => 'rma_order',
                    'label' => __('Associated order'),
                    'id' => 'rma_order',
                    'title' => __('Associated order'),
                    'href' => $this->getUrl('sales/order/view', ['order_id' => $rma->getrma_order_id()])
                ]
            );
        }

        $baseFieldset->addField(
            'rma_status',
            'select',
            [
                'name' => 'rma_status',
                'label' => __('Status'),
                'id' => 'rma_status',
                'title' => __('Status'),
                'values' => $this->_statusList->toOptionArray(),
                'class' => 'select'
            ]
        );

        $baseFieldset->addField(
            'rma_manager',
            'select',
            [
                'name' => 'rma_manager',
                'label' => __('Manager'),
                'id' => 'rma_manager',
                'title' => __('Manager'),
                'values' => $this->getManagerOptions(),
                'class' => 'select'
            ]
        );

        $baseFieldset->addField(
            'rma_expire_at',
            'text',
            [
                'name' => 'rma_expire_at',
                'label' => __('Valid until'),
                'id' => 'rma_expire_at',
                'title' => __('Valid until')
            ]
        );

        $baseFieldset->addField(
            'rma_shipping_address',
            'textarea',
            [
                'name' => 'rma_shipping_address',
                'label' => __('Shipping Address'),
                'id' => 'rma_shipping_address',
                'title' => __('Shipping Address')
            ]
        );

        $baseFieldset->addField(
            'rma_customer_comments',
            'textarea',
            [
                'name' => 'rma_customer_comments',
                'label' => __('Customer comments'),
                'id' => 'rma_customer_comments',
                'title' => __('Customer comments')
            ]
        );

        $baseFieldset->addField(
            'rma_private_comments',
            'textarea',
            [
                'name' => 'rma_private_comments',
                'label' => __('Private comments'),
                'id' => 'rma_private_comments',
                'title' => __('Private comments')
            ]
        );

        $baseFieldset->addField(
            'rma_public_comments',
            'textarea',
            [
                'name' => 'rma_public_comments',
                'label' => __('Public comments'),
                'id' => 'rma_public_comments',
                'title' => __('Public comments')
            ]
        );

        $data = $rma->getData();
        if ($rma->getOrder())
            $data['rma_order'] = 'Order #'.$rma->getOrder()->getincrement_id();
        if ($rma->getcustomer())
            $data['rma_customer_account'] = 'Customer #'.$rma->getCustomer()->getId();

        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getManagerOptions()
    {
        $users = [];
        foreach($this->_userList->create() as $user)
            $users[$user->getId()] = $user->getusername();
        return $users;
    }

}
