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



class Customweb_Barclaycard_Authorization_Ajax_InitParameterBuilder extends Customweb_Barclaycard_AbstractParameterBuilder {
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see Customweb_Barclaycard_AbstractParameterBuilder::buildParameters()
	 */
	public function buildParameters(){
		$this->addShopIdToCustomParameters();

		$methodParameters = $this->getPaymentMethod()->getPaymentMethodBrandAndMethod($this->getTransaction());
		$language = $this->getLanguageParameter();
		$orderParameters = $this->getOrderIdParameter();
		$paramPlus = $this->getParamPlusParameters();
		
		$storeAlias = 'N';
		if($this->getTransactionContext()->getAlias() !== null || $this->getTransactionContext()->createRecurringAlias()) {
			$storeAlias = 'Y';
		}
		
		$parameters = array(
			'ACCOUNT.PSPID' => $this->getConfiguration()->getActivePspId(),
			'ALIAS.ORDERID' => $orderParameters['ORDERID'],
			'ALIAS.STOREPERMANENTLY' => $storeAlias,
			'CARD.BRAND' => $methodParameters['brand'],
			'CARD.PAYMENTMETHOD' => $methodParameters['pm'],
			'LAYOUT.LANGUAGE' => $language['LANGUAGE'],
			'PARAMETERS.ACCEPTURL' => $this->getReturnUrlParameters(),
			'PARAMETERS.EXCEPTIONURL' => $this->getReturnUrlParameters(),
			'PARAMETERS.PARAMPLUS' => $paramPlus['PARAMPLUS'],
		);
		if($this->getTransactionContext()->getAlias() !== null && $this->getTransactionContext()->getAlias() != 'new'){
			$aliasParameters = $this->getTransactionContext()->getAlias()->getAuthorizationParameters();
			$parameters['ALIAS.ALIASID'] = $aliasParameters['ALIAS'];
		}
				
		$parameters['SHASIGNATURE.SHASIGN'] = Customweb_Barclaycard_Util::calculateHash($parameters, 'IN', $this->getConfiguration());
		
		// The template for the Flex checkout is currently ignored at Barclaycard, so we do not bother to send it.
		// For whatever reason the LAYOUT.TP parameters must not be used in the siganture.
		
		
		return $parameters;
	}
	protected function getReturnUrlParameters() {
		return $this->getEndpointAdapter()->getUrl('process', 'hosted', array('cwTransId' => $this->getTransaction()->getExternalTransactionId()));
		
	}
	
}