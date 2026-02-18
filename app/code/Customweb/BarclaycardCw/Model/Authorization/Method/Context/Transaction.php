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

namespace Customweb\BarclaycardCw\Model\Authorization\Method\Context;

class Transaction extends AbstractContext
{
	/**
	 * @var \Customweb\BarclaycardCw\Model\Authorization\TransactionFactory
	 */
	protected $_transactionFactory;

	/**
	 * @var \Customweb\BarclaycardCw\Model\Authorization\Transaction
	 */
	protected $transaction;

	/**
	 * @param \Magento\Framework\Registry $coreRegistry
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Customweb\BarclaycardCw\Model\Authorization\OrderContextFactory $orderContextFactory
	 * @param \Customweb\BarclaycardCw\Model\Authorization\CustomerContextFactory $customerContextFactory
	 * @param \Customweb\BarclaycardCw\Model\Authorization\TransactionFactory $transactionFactory
	 * @param \Customweb\BarclaycardCw\Model\Authorization\Transaction $transaction
	 * @param \Magento\Sales\Model\Order $order
	 * @param array $parameters
	 */
	public function __construct(
			\Magento\Framework\Registry $coreRegistry,
			\Magento\Checkout\Model\Session $checkoutSession,
			\Magento\Framework\App\RequestInterface $request,
			\Customweb\BarclaycardCw\Model\Authorization\OrderContextFactory $orderContextFactory,
			\Customweb\BarclaycardCw\Model\Authorization\CustomerContextFactory $customerContextFactory,
			\Customweb\BarclaycardCw\Model\Authorization\TransactionFactory $transactionFactory,
			\Customweb\BarclaycardCw\Model\Authorization\Transaction $transaction = null,
			\Magento\Sales\Model\Order $order = null,
			array $parameters = null
	) {
		parent::__construct($coreRegistry, $checkoutSession, $request, $orderContextFactory, $customerContextFactory);
		$this->_transactionFactory = $transactionFactory;

		if (!($transaction instanceof \Customweb\BarclaycardCw\Model\Authorization\Transaction)) {
			if ($order instanceof \Magento\Sales\Model\Order) {
				$transaction = $this->_transactionFactory->create()->loadByOrderId($order->getId());
			} else {
				$transaction = $this->_transactionFactory->create()->loadByOrderId($this->_checkoutSession->getLastRealOrder()->getId());
			}
		}
		$this->transaction = $transaction;

		$this->parameters = $parameters;
	}

	public function getPaymentMethod()
	{
		return $this->getTransaction()->getTransactionObject()->getPaymentMethod();
	}

	public function getOrderContext()
	{
		return $this->getTransaction()->getTransactionObject()->getTransactionContext()->getOrderContext();
	}

	public function getTransaction()
	{
		return $this->transaction;
	}

	public function getOrder()
	{
		return $this->getTransaction()->getOrder();
	}

	public function getQuote()
	{
		return $this->getTransaction()->getQuote();
	}

	public function isMoto()
	{
		return $this->getTransaction()->getTransactionObject() != null
			&& $this->getTransaction()->getTransactionObject()->getAuthorizationMethod() == \Customweb_Payment_Authorization_Moto_IAdapter::AUTHORIZATION_METHOD_NAME;
	}
}