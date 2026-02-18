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

namespace Customweb\BarclaycardCw\Model;

class DependencyContainer implements \Customweb_DependencyInjection_IContainer {

	/**
	 * @var \Customweb_DependencyInjection_IContainer
	 */
	private $container;

	/**
	 * @var \Customweb\BarclaycardCw\Model\Adapter\Endpoint
	 */
	private $_endpointAdapter;

	/**
	 * @var \Customweb\BarclaycardCw\Model\Adapter\Configuration
	 */
	private $_configurationAdapter;

	/**
	 * @var \Customweb\BarclaycardCw\Model\Adapter\StorageBackend
	 */
	private $_storageBackendAdapter;

	/**
	 * @var \Customweb\BarclaycardCw\Model\Adapter\LayoutRenderer
	 */
	private $_layoutRenderer;

	/**
	 * @var \Customweb\BarclaycardCw\Model\Authorization\TransactionHandler
	 */
	private $_transactionHandler;

	/**
	 * @var \Customweb\BarclaycardCw\Model\Update\Handler
	 */
	private $_updateHandler;

	/**
	 * @var \Customweb\BarclaycardCw\Model\Asset\CompositeResolver
	 */
	private $_assetResolver;

	/**
	 * @var \Customweb\BarclaycardCw\Model\BackendOperation\CancelAdapter
	 */
	private $_cancelAdapter;

	/**
	 * @var \Customweb\BarclaycardCw\Model\BackendOperation\CaptureAdapter
	 */
	private $_captureAdapter;

	/**
	 * @var \Customweb\BarclaycardCw\Model\BackendOperation\RefundAdapter
	 */
	private $_refundAdapter;

	/**
	 * @var \Customweb\BarclaycardCw\Model\ExternalCheckout\Service
	 */
	private $_externalCheckoutService;

	/**
	 * @param \Customweb\BarclaycardCw\Model\Adapter\Endpoint $endpointAdapter
	 * @param \Customweb\BarclaycardCw\Model\Adapter\Configuration $configurationAdapter
	 * @param \Customweb\BarclaycardCw\Model\Adapter\StorageBackend $storageBackendAdapter
	 * @param \Customweb\BarclaycardCw\Model\Adapter\LayoutRenderer $layoutRenderer
	 * @param \Customweb\BarclaycardCw\Model\Authorization\TransactionHandler $transactionHandler
	 * @param \Customweb\BarclaycardCw\Model\Update\Handler $updateHandler
	 * @param \Customweb\BarclaycardCw\Model\Asset\CompositeResolver $assetResolver
	 * @param \Customweb\BarclaycardCw\Model\BackendOperation\CancelAdapter $cancelAdapter
	 * @param \Customweb\BarclaycardCw\Model\BackendOperation\CaptureAdapter $captureAdapter
	 * @param \Customweb\BarclaycardCw\Model\BackendOperation\RefundAdapter $refundAdapter,
	 * @param \Customweb\BarclaycardCw\Model\ExternalCheckout\Service $externalCheckoutService
	 */
	public function __construct(
			\Customweb\BarclaycardCw\Model\Adapter\Endpoint $endpointAdapter,
			\Customweb\BarclaycardCw\Model\Adapter\Configuration $configurationAdapter,
			\Customweb\BarclaycardCw\Model\Adapter\StorageBackend $storageBackendAdapter,
			\Customweb\BarclaycardCw\Model\Adapter\LayoutRenderer $layoutRenderer,
			\Customweb\BarclaycardCw\Model\Authorization\TransactionHandler $transactionHandler,
			\Customweb\BarclaycardCw\Model\Update\Handler $updateHandler,
			\Customweb\BarclaycardCw\Model\Asset\CompositeResolver $assetResolver,
			\Customweb\BarclaycardCw\Model\BackendOperation\CancelAdapter $cancelAdapter,
			\Customweb\BarclaycardCw\Model\BackendOperation\CaptureAdapter $captureAdapter,
			\Customweb\BarclaycardCw\Model\BackendOperation\RefundAdapter $refundAdapter,
			\Customweb\BarclaycardCw\Model\ExternalCheckout\Service $externalCheckoutService
	) {
		$this->_endpointAdapter = $endpointAdapter;
		$this->_configurationAdapter = $configurationAdapter;
		$this->_storageBackendAdapter = $storageBackendAdapter;
		$this->_layoutRenderer = $layoutRenderer;
		$this->_transactionHandler = $transactionHandler;
		$this->_updateHandler = $updateHandler->setContainer($this);
		$this->_assetResolver = $assetResolver;
		$this->_cancelAdapter = $cancelAdapter;
		$this->_captureAdapter = $captureAdapter;
		$this->_refundAdapter = $refundAdapter;
		$this->_externalCheckoutService = $externalCheckoutService->setContainer($this);
		$this->initContainer();
	}

	public function getBean($identifier) {
		return $this->container->getBean($identifier);
	}

	public function getBeansByType($type) {
		return $this->container->getBeansByType($type);
	}

	public function hasBean($identifier) {
		return $this->container->hasBean($identifier);
	}

	private function initContainer() {
		\Customweb_Core_Util_Class::registerClassLoader(function($className) {
			return \Magento\Framework\Autoload\AutoloaderRegistry::getAutoloader()->loadClass($className);
		});

		$packages = array(
			0 => 'Customweb_Barclaycard',
 			1 => 'Customweb_Payment_Authorization',
 		);
		$packages[] = 'Customweb_BarclaycardCw_Model_';
		$packages[] = 'Customweb_Mvc_Template_Php_Renderer';
		$packages[] = 'Customweb_Payment_SettingHandler';

		\Customweb_Core_Util_Class::loadLibraryClassByName('Customweb_DependencyInjection_Bean_Provider_Annotation_Bean');
		$provider = new \Customweb_DependencyInjection_Bean_Provider_Editable(new \Customweb_DependencyInjection_Bean_Provider_Annotation(
				$packages
		));
		$provider->addObject(\Customweb_Core_Http_ContextRequest::getInstance());
		$provider->addObject($this->_endpointAdapter);
		$provider->addObject($this->_configurationAdapter);
		$provider->addObject($this->_storageBackendAdapter);
		$provider->addObject($this->_layoutRenderer);
		$provider->addObject($this->_transactionHandler);
		$provider->addObject($this->_updateHandler);
		$provider->addObject($this->_assetResolver);
		$provider->addObject($this->_cancelAdapter);
		$provider->addObject($this->_captureAdapter);
		$provider->addObject($this->_refundAdapter);
		$provider->addObject($this->_externalCheckoutService);
		$this->container = new \Customweb_DependencyInjection_Container_Default($provider);
	}
}