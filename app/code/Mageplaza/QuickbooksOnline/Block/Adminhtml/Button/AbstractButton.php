<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_QuickbooksOnline
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\QuickbooksOnline\Block\Adminhtml\Button;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Backend\Block\Widget\Context as WidgetContext;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Mageplaza\QuickbooksOnline\Helper\Data;

/**
 * Class AbstractButton
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\Button
 */
abstract class AbstractButton extends Template implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * AbstractButton constructor.
     *
     * @param WidgetContext $widgetContext
     * @param Registry $registry
     * @param CustomerFactory $customerFactory
     * @param Data $helperData
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        WidgetContext $widgetContext,
        Registry $registry,
        CustomerFactory $customerFactory,
        Data $helperData,
        Context $context,
        array $data = []
    ) {
        $this->urlBuilder      = $widgetContext->getUrlBuilder();
        $this->registry        = $registry;
        $this->customerFactory = $customerFactory;
        $this->helperData      = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     *
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data  = [];
        $model = $this->getModel();
        if ($model->getId() && !$model->getQuickbooksEntity() && $this->helperData->isEnabled()) {
            $data = $this->getOptions($model->getId());
        }

        return $data;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getPathUrl()
    {
        return 'mpquickbooks/product/add';
    }

    /**
     * @param string $id
     */
    public function addButton($id)
    {
        if ($this->helperData->isEnabled()) {
            $this->getToolbar()->addChild(
                'add_to_queue',
                Button::class,
                $this->getOptions($id)
            );
        }
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function getOptions($id)
    {
        $message = __('Are you sure you want to do this?');
        $url     = $this->getUrl($this->getPathUrl(), $this->getParamUrl($id));

        return [
            'label'      => __('Add To Quickbooks Queue'),
            'class'      => 'add_to_quickbooks',
            'on_click'   => "confirmSetLocation('{$message}', '{$url}')",
            'sort_order' => 1,
        ];
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function getParamUrl($id)
    {
        return ['id' => $id];
    }
}
