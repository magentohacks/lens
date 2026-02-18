<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_BarclaycardCw
 * 
 */

namespace Customweb\BarclaycardCw\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckoutSubmitAfter implements ObserverInterface
{
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;

	/**
	 * Core registry
	 *
	 * @var \Magento\Quote\Model\Quote
	 */
	protected $quoteFactory;

	/**
	 * @param \Magento\Framework\Registry $coreRegistry
	 */
	public function __construct(
		\Magento\Quote\Model\QuoteFactory $quoteFactory, 
		\Magento\Framework\Registry $coreRegistry
	) {
		$this->quoteFactory = $quoteFactory;
		$this->_coreRegistry = $coreRegistry;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/* @var $order \Magento\Sales\Model\Order */
		$order = $observer->getEvent()->getOrder();
		$this->quoteFactory->create()->load($order->getQuoteId())->setIsActive(0)->save();
		$this->_coreRegistry->unregister('barclaycardcw_checkout_last_order');
		$this->_coreRegistry->register('barclaycardcw_checkout_last_order', $order);
	}
}