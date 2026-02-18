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
namespace Mageplaza\QuickbooksOnline\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\QuickbooksOnline\Helper\Data as HelperData;

/**
 * Class Button
 * @package Mageplaza\QuickbooksOnline\Block\Adminhtml\System\Config
 */
class Button extends Field
{
    /**
     * @var string
     */
    protected $_template = 'system/config/button.phtml';

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Button constructor.
     *
     * @param Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope();

        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label' => $originalData['button_label'],
                'button_url'   => $this->getUrl($originalData['button_url'], ['_current' => true]),
                'html_id'      => $element->getHtmlId(),
            ]
        );

        return $this->_toHtml();
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAccessTokenUrl()
    {
        $authUrl = $this->helperData->getAuthUrl();
        $url     = 'https://appcenter.intuit.com/connect/oauth2?';
        $url     .= 'client_id=' . $this->helperData->getClientId();
        $url     .= '&response_type=code&scope=com.intuit.quickbooks.accounting openid profile email phone address';
        $url     .= '&redirect_uri=' . $authUrl;
        $url     .= '&state=security_token%3D138r5719ru3e1%26url%3D' . $authUrl . '&';

        return $url;
    }
}
