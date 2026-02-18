<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Themesettings
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Themesettings\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filter\LocalizedToNormalized;

class Add extends \Magento\Checkout\Controller\Cart\Add
{

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();


	   if(isset($params['ves']) && isset($params['refresh'])){
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode([])
                );
        }else{
           // return parent::execute();

		if (!$this->_formKeyValidator->validate($this->getRequest())) {
		    return $this->resultRedirectFactory->create()->setPath('*/*/');
		}

		$params = $this->getRequest()->getParams();
		try {
		    if (isset($params['qty'])) {
		        $filter = new LocalizedToNormalized(
		            ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
		        );
		        $params['qty'] = $filter->filter($params['qty']);
		    }
		    if (isset($params['left']['qty'])) {
		        $filter = new LocalizedToNormalized(
		            ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
		        );
		        $params['left']['qty'] = $filter->filter($params['left']['qty']);
		    }
		    if (isset($params['right']['qty'])) {
		        $filter = new LocalizedToNormalized(
		            ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
		        );
		        $params['right']['qty'] = $filter->filter($params['right']['qty']);
		    }		

		    $product = $this->_initProduct();
		    $related = $this->getRequest()->getParam('related_product');

		    /**
		     * Check product availability
		     */
		    if (!$product) {
		        return $this->goBack();
		    }


		    //print_r($params);exit;
		    $leftparams = array();
		    $rightparams = array();

		   $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();

		    if(isset($params['left'])){	
			$leftparams['uenc']= 	$params['uenc'];
			$leftparams['product'] = $params['product'];
	    		$leftparams['selected_configurable_option'] = $params['selected_configurable_option'];
	    		$leftparams['related_product'] = $params['related_product'];
	    		$leftparams['form_key'] = $params['form_key'];
			$leftparams['super_attribute'] = $params['left']['super_attribute'];
			$leftparams['qty'] = $params['left']['qty'];
			//print_r($leftparams);
			$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId($storeId)->load($params['product']);
			$this->cart->addProduct($product, $leftparams);
		    }
		    if(isset($params['right'])){
			$rightparams['uenc']= 	$params['uenc'];
			$rightparams['product'] = $params['product'];
	    		$rightparams['selected_configurable_option'] = $params['selected_configurable_option_right'];
	    		$rightparams['related_product'] = $params['related_product'];
	    		$rightparams['form_key'] = $params['form_key'];
			$rightparams['super_attribute'] = $params['right']['super_attribute'];
			$rightparams['qty'] = $params['right']['qty'];
			//print_r($rightparams);
		
			$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId($storeId)->load($params['product']);
			$this->cart->addProduct($product, $rightparams);
		    }

		    if(!isset($params['right']) && !isset($params['left'])){
			$this->cart->addProduct($product, $params);
		    }	
		    
		    if (!empty($related)) {
		        $this->cart->addProductsByIds(explode(',', $related));
		    }

		    $this->cart->save();

		    /**
		     * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
		     */
		    $this->_eventManager->dispatch(
		        'checkout_cart_add_product_complete',
		        ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
		    );

		    if (!$this->_checkoutSession->getNoCartRedirect(true)) {
		        if (!$this->cart->getQuote()->getHasError()) {
		            $message = __(
		                'You added %1 to your shopping cart.',
		                $product->getName()
		            );
		            $this->messageManager->addSuccessMessage($message);
		        }
		        return $this->goBack(null, $product);
		    }
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
		    if ($this->_checkoutSession->getUseNotice(true)) {
		        $this->messageManager->addNotice(
		            $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
		        );
		    } else {
		        $messages = array_unique(explode("\n", $e->getMessage()));
		        foreach ($messages as $message) {
		            $this->messageManager->addError(
		                $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($message)
		            );
		        }
		    }

		    $url = $this->_checkoutSession->getRedirectUrl(true);

		    if (!$url) {
		        $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
		        $url = $this->_redirect->getRedirectUrl($cartUrl);
		    }

		    return $this->goBack($url);

		} catch (\Exception $e) {
		    //$this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
		    $this->messageManager->addException($e, $e->getMessage());	
		    $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
		    return $this->goBack();
		}


        }
    }

    /**
     * Resolve response
     *
     * @param string $backUrl
     * @param \Magento\Catalog\Model\Product $product
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    protected function goBack($backUrl = null, $product = null)
    {
        $params = $this->getRequest()->getParams();

        if(isset($params['ves']) && isset($params['refresh'])){
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode([])
                );
        }

        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                'statusText' => __('Out of stock')
                ];
            }
        }

        if($product){
            if(isset($params['ves'])){
                $result['html'] = $this->_view->getLayout()->createBlock("Magento\Framework\View\Element\Template")
                ->assign("product", $product)
                ->setTemplate("Ves_Themesettings::ajax/cart_success.phtml")
                ->toHtml();
            }
        }

        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
            );
    }
}
