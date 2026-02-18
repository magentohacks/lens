<?php
/**
 * @category    MP
 * @package     MP_AutoApplyCoupon
 * @copyright   MagePhobia (http://www.magephobia.com)
 */

namespace MP\AutoApplyCoupon\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as Observer;

class AutoApplyCouponObserver implements ObserverInterface
{
    protected $_request;
    protected $_customerSession;
    protected $_checkoutSession;
    protected $_quote;
    protected $_cartHelper;
    protected $_salesCoupon;
    protected $_salesRule;
    protected $_genericSession;
    protected $_cookieManager;
    protected $_cookieMetadataFactory;
    protected $_cookieMetadata;
    protected $_messageManager;
    protected $_response;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\Generic $genericSession,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\SalesRule\Model\Coupon $salesCoupon,
        \Magento\SalesRule\Model\Rule $salesRule,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_request = $request;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_quote = $quote;
        $this->_cartHelper = $cartHelper;
        $this->_salesCoupon = $salesCoupon;
        $this->_salesRule = $salesRule;
        $this->_genericSession = $genericSession;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_messageManager = $messageManager;
        $this->_response = $response;
        $this->_storeManager = $storeManager;
    }

    public function setCookieMetadata()
    {
        $this->_cookieMetadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()
            ->setDuration(time() + 86400)
            ->setPath('/');
    }

    public function getCookieMetadata()
    {
        return $this->_cookieMetadata;
    }

    public function execute(Observer $observer)
    {
        $this->setCookieMetadata();
        $params = array_change_key_case($this->_request->getParams(), CASE_LOWER);
        if (isset($params['quote_id'])) {
            $quote = $this->_quote->load(intval($params['quote_id']));
            if (is_object($quote) && $quote->getId() && $quote->getIsActive()) {
                if ($this->_customerSession->isLoggedIn() && $quote->getCustomerId()) {
                    if ($quote->getCustomerId() != $this->_customerSession->getCustomer()->getId()) {
                        $this->_customerSession->logout();
                        $this->_response->setRedirect('/customer/account/login', 301)->sendResponse();
                    } else {
                        $this->_checkoutSession->setQuoteId($quote->getId());
                    }
                } elseif ($this->_customerSession->isLoggedIn() && !$quote->getCustomerId()) {
                    $this->_checkoutSession->setQuoteId($quote->getId());
                } elseif (!$this->_customerSession->isLoggedIn()) {
                    if ($quote->getCustomerId()) {
                        $this->_response->setRedirect('/customer/account/login', 301)->sendResponse();
                    } else {
                        $this->_checkoutSession->setQuoteId($quote->getId());
                    }
                }
            } else {
                $this->_messageManager->addNotice('Sorry, looks like you\'ve got Invalid Cart'
                    . ' ID or you have already made a purchase with the cart you are trying to access. Thank you!');
            }
        }

        $discountEmail = '';
        if (array_key_exists('discount-email', $params) !== false) {
            $discountEmail = $params['discount-email'];
        } elseif (array_key_exists('email', $params) !== false) {
            $discountEmail = $params['email'];
        } elseif (array_key_exists('utm_email', $params) !== false) {
            $discountEmail = $params['utm_email'];
        }
        if (isset($discountEmail)) {
            if ($discountEmail != '') {
                if (\Zend_Validate::is($discountEmail, 'EmailAddress')) {
                    if ($this->_cartHelper->getItemsCount()) {
                        $this->_checkoutSession->getQuote()->setCustomerEmail($discountEmail)->save();
                        $this->_cookieManager->deleteCookie('discount-email', $this->getCookieMetadata());
                    } else {
                        $this->_cookieManager->setPublicCookie('discount-email', $discountEmail, $this->getCookieMetadata());
                    }
                }
            }
        }

        if (isset($params['coupon']) || (isset($params['utm_promocode']))) {
            if (isset($params['coupon'])) {
                $coupon = $params['coupon'];
            }

            if (isset($params['utm_promocode'])) {
                $coupon = $params['utm_promocode'];
            }

            if ($coupon != '') {
                if ($this->_isCouponValid($coupon)) {
                    if ($this->_cartHelper->getItemsCount()) {
                        $this->_checkoutSession->getQuote()->setCouponCode($coupon)->save();
                        $this->_cookieManager->deleteCookie('discount_code', $this->getCookieMetadata());
                    } else {
                        $this->_cookieManager->setPublicCookie('discount_code', $coupon, $this->getCookieMetadata());
                    }
                }
            }
        } else {
            $this->checkoutCartAddProductComplete();
        }
    }

    public function checkoutCartAddProductComplete()
    {
        $coupon = $this->_cookieManager->getCookie('discount_code');
        if (($coupon) && ($this->_isCouponValid($coupon)) && ($this->_cartHelper->getItemsCount())) {
            $this->_checkoutSession->getQuote()->setCouponCode($coupon)->save();
            $this->_cookieManager->deleteCookie('discount_code', $this->getCookieMetadata());
        }

        $email = $this->_cookieManager->getCookie('discount-email');
        if ($email && \Zend_Validate::is($email, 'EmailAddress')) {
            if ($this->_cartHelper->getItemsCount()) {
                $this->_checkoutSession->getQuote()->setCustomerEmail($email)->save();
                $this->_cookieManager->deleteCookie('discount-email', $this->getCookieMetadata());
            }
        }
    }

    protected function _isCouponValid($couponCode)
    {
        try {
            $coupon = $this->_salesCoupon->load($couponCode, 'code');
            if (is_object($coupon)) {
                $rule = $this->_salesRule->load($coupon->getRuleId());
                if (is_object($rule)) {
                    $conditionsUnSerialized = unserialize($rule->getConditionsSerialized());
                    if ($rule->getIsActive()) {
                        if (is_array($conditionsUnSerialized) && (isset($conditionsUnSerialized['conditions']))
                            && (is_array($conditionsUnSerialized['conditions']))
                        ) {
                            foreach ($conditionsUnSerialized['conditions'] as $condition) {
                                if (isset($condition['attribute']) && ($condition['attribute'] == 'base_subtotal')
                                    && (isset($condition['operator'])) && ($condition['operator'] == '>=')
                                    && (isset($condition['value'])) && ($condition['value'] > 0)
                                    && ($this->_checkoutSession->getQuote()->getSubtotal() < $condition['value'])
                                ) {
                                    $this->_cookieManager->setPublicCookie('discount_code', $couponCode, $this->getCookieMetadata());
                                    return false;
                                }
                            }
                        }
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
