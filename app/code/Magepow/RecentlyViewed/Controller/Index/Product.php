<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2020-05-19 18:29:13
 * @@Function:
 */

namespace Magepow\RecentlyViewed\Controller\Index;

use Magento\Framework\Controller\ResultFactory; 

class Product extends \Magepow\RecentlyViewed\Controller\Index
{
    /**
     * Default customer account page.
     */
    public function execute()
    {
		if ($this->getRequest()->isAjax()) {
	        $this->_view->loadLayout();
	        // $this->_view->renderLayout();
	        $productIds = $this->getRequest()->getParam('product_ids');
	        $limit = $this->getRequest()->getParam('limit');
			if(!$limit) $limit = 10;
		 	$response = $this->_view->getLayout()->getBlock('GridProduct');
			if($response){
				$response = $response->setData('product_ids', $productIds)
									->setData('limit', $limit)
									->toHtml();
			}
		    $this->getResponse()->setBody($response);
	    }else {
	        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
	        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
	        return $resultRedirect;
	    }
    }
}
