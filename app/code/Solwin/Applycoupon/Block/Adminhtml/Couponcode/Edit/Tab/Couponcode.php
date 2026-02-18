<?php

/**
 * Solwin Infotech
 * Solwin Discount Coupon Code Link Extension
 *
 * @category   Solwin
 * @package    Solwin_Applycoupon
 * @copyright  Copyright © 2006-2018 Solwin (https://www.solwininfotech.com)
 * @license    https://www.solwininfotech.com/magento-extension-license/
 */

namespace Solwin\Applycoupon\Block\Adminhtml\Couponcode\Edit\Tab;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Couponcode extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

    /**
     * @var \Solwin\Applycoupon\Model\Couponcode\Source\Status
     */
    protected $_statusOptions;

    /**
     * @var \Solwin\Applycoupon\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Solwin\Applycoupon\Model\CouponcodeFactory
     */
    protected $_modelCouponcodeFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * constructor
     *
     * @param \Solwin\Applycoupon\Model\Couponcode\Source\Status $statusOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        UrlInterface $urlInterface,
        \Solwin\Applycoupon\Model\Couponcode\Source\Status $statusOptions,
        \Solwin\Applycoupon\Model\CouponcodeFactory $modelCouponcodeFactory,
        \Solwin\Applycoupon\Helper\Data $helper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->_urlInterface =  $urlInterface;
        $this->_storeManager = $storeManager;
        $this->_statusOptions = $statusOptions;
        $this->_helper = $helper;
        $this->_modelCouponcodeFactory = $modelCouponcodeFactory;
        $this->authSession = $authSession;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm() {        
        // by default: URL_TYPE_LINK is returned
        $url = $this->_storeManager->getStore()->getBaseUrl();
        
        /** @var \Solwin\Applycoupon\Model\Couponcode $couponcode */
        $couponcode = $this->_coreRegistry
                ->registry('solwin_applycoupon_couponcode');
        $shareLink = $this->_helper
                    ->getConfigValue(
                    'applycouponsection/applycoupongroup/share_link'
            );
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('couponcode_');
        $form->setFieldNameSuffix('couponcode');
        $fieldset = $form->addFieldset(
                'base_fieldset', [
            'legend' => __('Couponcode Information'),
            'class' => 'fieldset-wide'
                ]
        );
        if ($couponcode->getId()) {
            $fieldset->addField(
                    'couponcode_id', 'hidden', ['name' => 'couponcode_id']
            );
            $fieldset->addField(
                    'emailaction', 'hidden', ['name' => 'emailaction', 'class' => 'emailaction', 'value' => $this->getUrl('solwin_applycoupon/couponcode/mailcouponcode')]
            );
        }
        $fieldset->addField(
                'rule_name', 'text', [
            'name' => 'rule_name',
            'label' => __('Rule Name'),
            'title' => __('Rule Name'),
            'required' => true,
                ]
        );
        $fieldset->addField(
                'coupon_code', 'select', [
            'name' => 'coupon_code',
            'label' => __('Coupon Code'),
            'title' => __('Coupon Code'),
            'required' => true,
            'note' => __('Select Coupon code that you have created '
                    . 'under <strong>Marketing -> Promotion -> Cart Price '
                    . 'Rules</strong>'),
            'values' => array_merge(
                        ['' => '-Select-'], $this->_helper->getCouponList()
                    ),
            'after_element_html' =>
                    '<script>
                        require(["jquery"], function ($) {
                            $("#couponcode_link_with_redirection, #couponcode_link_without_redirection").focus(function(){
                                var getStoreFrontUrl ="'.$url.'";
                                var val = $("#couponcode_coupon_code").val();
                                if(val != "") {
                                    var createCommentWithoutRedirect = getStoreFrontUrl+\'?coupon=\'+val+\'&return_url=no\';
                                    var createCommentWithRedirect = getStoreFrontUrl+\'?coupon=\'+val+\'&return_url=\'+getStoreFrontUrl;
                                    if($("#couponcode_link_without_redirection").val() == "") {
                                        $("#couponcode_link_without_redirection").val(createCommentWithoutRedirect);
                                    }
                                    if($("#couponcode_link_with_redirection").val() == "") {
                                        $("#couponcode_link_with_redirection").val(createCommentWithRedirect);
                                    }
                                }
                            });
                        });
                    </script>',
                ]
        );

        if ($shareLink == 0) {
            $fieldset->addField(
                    'link_with_redirection', 'textarea', [
                'name' => 'link_with_redirection',
                'label' => __('Link With Redirection'),
                'title' => __('Link With Redirection'),
                'note' => __('Use "Link with redirection" in case of email, '
                        . 'newsletter or any promotion.<br><br><b>Example: </b>https://example.com?coupon=<b>[your_coupon_code]</b>&return_url=<b>[redirect_url]</b>'),
                    ]
            );
        } else {
            $fieldset->addField(
                    'link_without_redirection', 'textarea', [
                'name' => 'link_without_redirection',
                'label' => __('Link Without Redirection'),
                'title' => __('Link Without Redirection'),
                'note' => __('Use "Link without redirection" in case if you want to'
                        . ' show banner in this website like "50% off" and you want'
                        . ' customer to stay on same page when user click on it.<br><br><b>Example: </b>https://example.com?coupon=<b>[your_coupon_code]</b>&return_url=no'),
                    ]
            );
        }

        if ($couponcode->getId()) {
            $couponId = $couponcode->getId();
            $couponModel = $this->_modelCouponcodeFactory->create();
            $couponCollection = $couponModel->load($couponId);
            $linkWithRedirection = $couponCollection->getLinkWithRedirection();
            $linkWithoutRedirection = $couponCollection->getLinkWithoutRedirection();
            $redirectionLink = '';
            if ($shareLink == 0) {
                $redirectionLink = $linkWithRedirection;
            } else {
                $redirectionLink = $linkWithoutRedirection;
            }
            $fbImage = $this->getDefaultImg('facebook_share.png');
            $twitterImage = $this->getDefaultImg('twitter_share.png');
            $googleImage = $this->getDefaultImg('google_share.png');
            $linkImage = $this->getDefaultImg('linkedin_share.png');
            $emailImage = $this->getDefaultImg('email_icon.png');
            $currentUser = $this->getCurrentUser()->getUsername();
            $commentText = 'Hi, '.$currentUser.' wants to share this coupon code link with you: '.$redirectionLink;
            $fieldset->addField(
                    'redirectionlink', 'hidden', ['name' => 'redirectionlink', 'class' => 'redirectionlink', 'value' => $redirectionLink]
            );
            if(!empty($redirectionLink)) {
                $fieldset->addField(
                        '', 'label', [
                    'name' => '',
                    'label' => __('Share Link On Social Media'),
                    'title' => __('Share Link On Social Media'),
                    'required' => false,
                    'note' => '<b>Note: </b>To share the coupon code link, please first save it.',
                    'after_element_html' => ''
                    . '<a href="https://www.facebook.com/sharer/sharer.php'
                    . '?u=' . urlencode($redirectionLink) . '" '
                    . 'target="_blank" class="solwin-social-icon">'
                    . '<img src="' . $fbImage . '" alt="Share on Facebook"></a>'
                    . '<a href="https://twitter.com/intent/tweet/'
                    . '?url=' . urlencode($redirectionLink) . '" '
                    . 'target="_blank" class="solwin-social-icon">'
                    . '<img src="' . $twitterImage . '" alt="Share on Twitter"></a>'
                    . '<a href="https://plus.google.com/share'
                    . '?url=' . urlencode($redirectionLink) . '" '
                    . 'target="_blank" class="solwin-social-icon">'
                    . '<img src="' . $googleImage . '" alt="Share on Google+">'
                    . '</a>'
                    . '<a href="https://www.linkedin.com/shareArticle'
                    . '?mini=true&url=' . urlencode($redirectionLink) . '" '
                    . 'target="_blank" class="solwin-social-icon">'
                    . '<img src="' . $linkImage . '" alt="Share on LinkedIn">'
                    . '</a>'
                    . '<a href="#" id="test" class="email-coupon"><img src="' . $emailImage . '" alt="Email"></a>'
                    . '<div id="email_popup">'
                    . '<div id="email-messages-success"><div class="messages"><div class="message message-success success"><div data-ui-id="messages-message-success">Email sent successfully to recepient.</div></div></div></div>'
                    . '<div id="email-messages-error"><div class="messages"><div class="message message-error error"><div data-ui-id="messages-message-success">Something went wrong while sending email.</div></div></div></div>'
                    . '<div class="field name required">'
                    . '<label class="label" for="name"><span>Name</span></label>'
                    . '<div class="control">'
                    . '<input name="name" id="name" title="Name" value="" class="input-text admin__control-text" type="text" />'
                    . '</div>'
                    . '</div>'
                    . '<div class="field email required">'
                    . '<label class="label" for="name"><span>Email</span></label>'
                    . '<div class="control">'
                    . '<input name="email" id="email" title="Email" value="" class="input-text admin__control-text" type="text" />'
                    . '</div>'
                    . '</div>'
                    . '<div class="field subject required">'
                    . '<label class="label" for="subject"><span>Subject</span></label>'
                    . '<div class="control">'
                    . '<input name="subject" id="subject" title="Subject" value="" class="input-text admin__control-text" type="text" />'
                    . '</div>'
                    . '</div>'
                    . '<div class="field comment required">'
                    . '<label class="label" for="comment"><span>Comment</span></label>'
                    . '<div class="control">'
                    . '<textarea name="comment" id="comment" title="Comment" class="input-text admin__control-text" cols="50" rows="5">'.$commentText.'</textarea>'
                    . '</div>'
                    . '</div>'
                    . '</div>'
                    . '<script>
                    require([\'jquery\', \'Magento_Ui/js/modal/modal\'], function ($, modal) {
                        var options = {
                            type: \'popup\', responsive: true, innerScroll: true, title: \'Email this link\', buttons: [{
                                    text: $.mage.__(\'OK\'),
                                    class: \'\',
                                    click: function () {
                                        var name = $(\'#name\').val();
                                        var email = $(\'#email\').val();
                                        var subject = $(\'#subject\').val();
                                        var comment = $(\'#comment\').val();
                                        var emailaction = $(\'.emailaction\').attr(\'value\');
                                        var redirectionlink = $(\'.redirectionlink\').attr(\'value\');
                                        jQuery.ajax({
                                            url: emailaction,
                                            timeout: 15000,
                                            type: "POST",
                                            showLoader: true,
                                            data: {name: name, email: email, subject: subject, comment: comment, redirectionlink: redirectionlink},
                                            complete: function (results) {
                                                $(\'#email_popup button\').on(\'click\', function () {
                                                    this.closeModal();
                                                });
                                            },
                                            success: function (results) {
                                                if(results == \'success\') {
                                                    $(\'#email-messages-success\').css(\'display\', \'block\');
                                                    $(\'#name\').val(\'\');
                                                    $(\'#subject\').val(\'\');
                                                    $(\'#email\').val(\'\');
                                                    $(\'#comment\').val(\'\');
                                                } else {
                                                    $(\'#email-messages-error\').css(\'display\', \'block\');
                                                }
                                            }

                                        });
                                    }
                                }, {
                                    text: $.mage.__(\'Cancel\'),
                                    class: \'\',
                                    click: function () {
                                        this.closeModal();
                                    }
                                }

                            ]
                        };
                        var popup = modal(options, $(\'#email_popup\'));
                        $(\'#test\').on(\'click\', function () {
                            $(\'#email-messages-success\').css(\'display\', \'none\');
                            $(\'#email-messages-error\').css(\'display\', \'none\');
                            $(\'#email_popup\').css(\'display\', \'block\');
                            $(\'#email_popup\').modal(\'openModal\');
                        });
                    });
                    </script>'
                        ]
                );
            }
        }

        if ($couponcode->getViewsCount()) {
            $fieldset->addField(
                    'views_count', 'label', [
                'name' => 'views_count',
                'label' => __('No. Of Views'),
                'title' => __('No. Of Views'),
                    ]
            );
        } else {
            $fieldset->addField(
                    'views_count', 'label', [
                'name' => '',
                'label' => __('No. Of Views'),
                'title' => __('No. Of Views'),
                'after_element_html' => '0',
                    ]
            );
        }

        $fieldset->addField(
                'status', 'select', [
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'required' => true,
            'values' => array_merge(
                    ['' => ''], $this->_statusOptions->toOptionArray()
            ),
                ]
        )->setAfterElementHtml('<style>.admin__field.field.field-views_count .admin__field-control {
          border: 1px dashed #ccc;padding: 0 5px;line-height: 3.2rem;} </style>');

        $couponcodeData = $this->_session
                ->getData('solwin_applycoupon_couponcode_data', true);
        if ($couponcodeData) {
            $couponcode->addData($couponcodeData);
        } else {
            if (!$couponcode->getId()) {
                $couponcode->addData($couponcode->getDefaultValues());
            }
        }
        $form->addValues($couponcode->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare images for share
     *
     * @return string
     */
    public function getDefaultImg($socialname = '') {
        return $this->_assetRepo
                        ->getUrl('Solwin_Applycoupon::images/' . $socialname);
    }

    /**
     * Get current logged in user name
     *
     * @return type
     */

    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel() {
        return __('Couponcode');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden() {
        return false;
    }

}
