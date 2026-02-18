<?php

namespace BoostMyShop\Rma\Block\Rma\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected $_coreRegistry;

    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('RMA Information'));
    }

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;

        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function getRma()
    {
        return $this->_coreRegistry->registry('current_rma');
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_section',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock('BoostMyShop\Rma\Block\Rma\Edit\Tab\Main')->toHtml(),
                'active' => true
            ]
        );

        if ($this->getRma()->getId())
        {
            $this->addTab(
                'items',
                [
                    'label' => __('Items'),
                    'title' => __('Items'),
                    'content' => $this->getLayout()->createBlock('BoostMyShop\Rma\Block\Rma\Edit\Tab\Items')->toHtml()
                ]
            );

            $this->addTab(
                'messages',
                [
                    'label' => __('Messages'),
                    'title' => __('Messages'),
                    'content' => $this->getLayout()->createBlock('BoostMyShop\Rma\Block\Rma\Edit\Tab\Messages')->toHtml()
                ]
            );

            $this->addTab(
                'history',
                [
                    'label' => __('History'),
                    'title' => __('History'),
                    'content' => $this->getLayout()->createBlock('BoostMyShop\Rma\Block\Rma\Edit\Tab\History')->toHtml()
                ]
            );
        }

        return parent::_beforeToHtml();
    }
}
