<?php

/**
 *  * You are allowed to use this API in your web application.
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
 */



/**
 *
 * @author Thomas Hunziker
 * @Bean
 *
 */
class Customweb_Barclaycard_Authorization_Ajax_Adapter extends Customweb_Barclaycard_Authorization_AbstractAdapter implements 
		Customweb_Payment_Authorization_Ajax_IAdapter {

	public function getAuthorizationMethodName(){
		return self::AUTHORIZATION_METHOD_NAME;
	}

	public function getAdapterPriority(){
		return 150;
	}

	public function getAjaxFileUrl(Customweb_Payment_Authorization_ITransaction $transaction){
		$assetResolver = $this->getContainer()->getBean('Customweb_Asset_IResolver');
		return (string) $assetResolver->resolveAssetUrl('hosted.js');
	}

	public function getJavaScriptCallbackFunction(Customweb_Payment_Authorization_ITransaction $transaction){
		if ($transaction->getPaymentMethod()->getPaymentMethodName() == 'creditcard' &&
				 $transaction->getTransactionContext()->getAlias() instanceof Customweb_Barclaycard_Authorization_Transaction) {
			$endpointAdapter = $this->getContainer()->getBean('Customweb_Payment_Endpoint_IAdapter');
			/* @var $endpointAdapter Customweb_Payment_Endpoint_IAdapter */
			/* @var $transaction Customweb_Barclaycard_Authorization_Transaction */
			$url = $endpointAdapter->getUrl('process', 'aliasCC',
					array(
						'cwTransId' => $transaction->getExternalTransactionId(),
						'cwHash' => $transaction->getSecuritySignature('process/aliasCC') 
					));
			//processAliasCC
			return "function (formFieldValues) { window.location = '$url'; }";
		}
		else {
			$builder = new Customweb_Barclaycard_Authorization_Ajax_InitParameterBuilder($transaction, $this->getContainer());
			$parameters = $builder->buildParameters();
			$iframeUrl = Customweb_Core_Url::_($this->getConfiguration()->getFlexCheckoutUrl())->appendQueryParameters($parameters)->toString();
			
			$cssUrl = $this->getContainer()->getBean('Customweb_Asset_IResolver')->resolveAssetUrl('hosted.css');
			
			$execute = '
					if(typeof window.jQuery == "undefined") {
						window.jQuery = cwjQuery;
					}
					barclaycardFlexCheckout.includeCss("' . $cssUrl . '");
					barclaycardFlexCheckout.createIframe("' . $iframeUrl . '", window.jQuery);';
			
			$complete = "function(formFieldValues) {" . Customweb_Util_JavaScript::getLoadJQueryCode(null, 'cwjQuery', 'function(){' . $execute . '}') .
					 '}';
			return $complete;
		}
	}

	public function createTransaction(Customweb_Payment_Authorization_Ajax_ITransactionContext $transactionContext, $failedTransaction){
		$transaction = new Customweb_Barclaycard_Authorization_Transaction($transactionContext);
		$transaction->setAuthorizationMethod(self::AUTHORIZATION_METHOD_NAME);
		
		$transaction->setLiveTransaction(!$this->getConfiguration()->isTestMode());
		
		return $transaction;
	}

	public function getVisibleFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $customerPaymentContext){
		return array();
		/*
		 * TODO: Check Forms
		 * $paymentMethod = $this->getPaymentMethodFactory()->getPaymentMethod($orderContext->getPaymentMethod(), self::AUTHORIZATION_METHOD_NAME);
		 * return $paymentMethod->getFormFields($orderContext, $aliasTransaction, $failedTransaction, self::AUTHORIZATION_METHOD_NAME, false,
		 * $customerPaymentContext);
		 */
	}

	/**
	 * This function handles the notification
	 *
	 * @param Customweb_Barclaycard_Authorization_Transaction $transaction
	 * @param array $parameters
	 * @return Customweb_Core_Http_Response
	 */
	public function processAuthorization(Customweb_Barclaycard_Authorization_Transaction $transaction, array $parameters){
		
		// In case the authorization failed, we stop processing here
		if ($transaction->isAuthorizationFailed()) {
			return $this->finalizeAuthorizationRequest($transaction);
		}
		
		// In case the transaction is authorized, we do not have to do anything here.        	 		  	   	 		 
		if ($transaction->isAuthorized()) {
			return $this->finalizeAuthorizationRequest($transaction);
		}
		
		$transaction->appendAuthorizationParameters($parameters);
		$parameters = array_change_key_case($parameters, CASE_UPPER);
		if (!$this->validateResponse($parameters)) {
			$transaction->setAuthorizationFailed(
					Customweb_I18n_Translation::__('The notification failed because the SHA signature seems not to be valid.'));
		}
		else {
			$this->setTransactionAuthorizationState($transaction, $parameters);
		}
		return $this->finalizeAuthorizationRequest($transaction);
	}

	public function processTokenCreation(Customweb_Barclaycard_Authorization_Transaction $transaction, array $parameters){
		$computed = Customweb_Barclaycard_Util::calculateHash($parameters, "out", $this->getConfiguration());
		$parameters = array_change_key_case($parameters, CASE_UPPER);
		if (!isset($parameters['SHASIGN']) || $parameters['SHASIGN'] != $computed) {
			$transaction->setAuthorizationFailed(Customweb_I18n_Translation::__("The SHA signatures do not match."));
			return Customweb_Barclaycard_Util::createBreakoutResponse($transaction->getFailedUrl());
		}
		$method = $this->getPaymentMethodByTransaction($transaction);
		$transaction->setAliasCreationResponse($parameters);
		
		$mantatoryParameters = array(
			'ALIAS_STATUS',
			'ALIAS_ALIASID' 
		);
		foreach ($mantatoryParameters as $paramName) {
			if (!isset($parameters[$paramName])) {
				$errorMessage = new Customweb_Payment_Authorization_ErrorMessage(
						Customweb_I18n_Translation::__('The payment failed due to technical difficulties.'),
						Customweb_I18n_Translation::__(
								'Missing return parameters for the tokenization, please check the dynamic parameter configuration in the Barclaycard backend. Missing Parameter: !parameterName',
								array(
									'!parameterName' => $paramName 
								)));
				$transaction->setAuthorizationFailed($errorMessage);
				return Customweb_Barclaycard_Util::createBreakoutResponse($transaction->getFailedUrl());
			}
		}
		
		if (isset($parameters['ALIAS_NCERROR']) && $parameters['ALIAS_NCERROR'] != '0') {
			$errorMessage = $method->getAliasCreationErrorMessage($parameters);
			$transaction->setAuthorizationFailed($errorMessage);
			return Customweb_Barclaycard_Util::createBreakoutResponse($transaction->getFailedUrl());
		}
		if ($parameters['ALIAS_STATUS'] == '1') {
			$transaction->setAuthorizationFailed(Customweb_I18n_Translation::__('The payment failed due to technical difficulties.'));
			return Customweb_Barclaycard_Util::createBreakoutResponse($transaction->getFailedUrl());
		}
		elseif ($parameters['ALIAS_STATUS'] == '3') {
			$transaction->setAuthorizationFailed(Customweb_I18n_Translation::__('The payment was successfully cancelled.'));
			return Customweb_Barclaycard_Util::createBreakoutResponse($transaction->getFailedUrl());
		}
		elseif ($parameters['ALIAS_STATUS'] == '0' || $parameters['ALIAS_STATUS'] == '2') {
			try {
				$builder = new Customweb_Barclaycard_Authorization_Ajax_DirectParameterBuilder($transaction, $this->getContainer(), $parameters);
				$response = Customweb_Barclaycard_Util::sendDirectRequest($this->getDirectOrderUrl(), $builder->buildParameters());
				
				unset($parameters['CVC']);
				unset($parameters['CARD_CVC']);
				
				$transaction->appendAuthorizationParameters($response);
				$transaction->appendAuthorizationParameters($parameters);
				
				$converted = array();
				if (isset($parameters['ALIAS_ALIASID'])) {
					$converted['ALIAS'] = $parameters['ALIAS_ALIASID'];
				}
				
				if (isset($parameters['CARD_CARDNUMBER'])) {
					$converted['CARDNO'] = $parameters['CARD_CARDNUMBER'];
				}
				if (isset($parameters['CARD_EXPIRYDATE'])) {
					$converted['ED'] = $parameters['CARD_EXPIRYDATE'];
				}
				if (isset($parameters['CARD_BRAND'])) {
					$converted['BRAND'] = $parameters['CARD_BRAND'];
				}
				$transaction->appendAuthorizationParameters($converted);
				
				// Check whether a 3D secure redirection is required or not.
				if (!$transaction->is3dRedirectionRequired()) {
					$this->setTransactionAuthorizationState($transaction, $response);
				}
			}
			catch (Exception $e) {
				$transaction->setAuthorizationFailed($e->getMessage());
			}
			
			if ($transaction->isAuthorizationFailed()) {
				return Customweb_Barclaycard_Util::createBreakoutResponse($transaction->getFailedUrl());
			}
			
			if ($transaction->isAuthorized()) {
				return Customweb_Barclaycard_Util::createBreakoutResponse($transaction->getSuccessUrl());
			}
			
			// Handle 3D secure case
			if (!$transaction->isAuthorized()) {
				if ($transaction->is3dRedirectionRequired()) {
					$url = $this->getEndpointAdapter()->getUrl('process', 'redirect3d',
							array(
								'cwTransId' => $transaction->getExternalTransactionId(),
								'cwHash' => $transaction->getSecuritySignature('process/redirect3d') 
							));
					
					return Customweb_Barclaycard_Util::createBreakoutResponse($url);
				}
			}
			return Customweb_Core_Http_Response::_("The transaction is in a bad state.");
		}
		else {
			$transaction->setAuthorizationFailed(Customweb_I18n_Translation::__('The payment failed due to technical difficulties.'));
			return Customweb_Barclaycard_Util::createBreakoutResponse($transaction->getFailedUrl());
		}
	}

	public function finalizeAuthorizationRequest(Customweb_Payment_Authorization_ITransaction $transaction){
		return new Customweb_Core_Http_Response();
	}
}