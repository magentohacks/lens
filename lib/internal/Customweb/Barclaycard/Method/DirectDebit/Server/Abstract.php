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
 */
abstract class Customweb_Barclaycard_Method_DirectDebit_Server_Abstract extends Customweb_Barclaycard_Method_DefaultMethod {

	public function getFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $authorizationMethod, $isMoto, $customerPaymentContext){
		$additionalData = array();
		
		$schema = '{year}-{month}-{day}: {random}';
		if ($this->existsPaymentMethodConfigurationValue('sepa_mandate_id_schema')) {
			$schema = $this->getPaymentMethodConfigurationValue('sepa_mandate_id_schema');
		}
		$mandateId = Customweb_Barclaycard_Util::cleanMandateIdExtended(Customweb_Payment_Authorization_Method_Sepa_Mandate::generateMandateId($schema));
		Customweb_Payment_Authorization_Method_Sepa_Mandate::setMandateIdIntoCustomerContext($customerPaymentContext, $mandateId, $this);
		
		$builder = new Customweb_Payment_Authorization_Method_Sepa_ElementBuilder();
		$builder->setMandateId($mandateId);
		
		if ($aliasTransaction instanceof Customweb_Barclaycard_Authorization_Transaction) {
			$parameters = $aliasTransaction->getAuthorizationParameters();
			$aliasForDisplay = $parameters['CARDNO'];
			$alias = $parameters['ALIAS'];
			
			$ibanControl = new Customweb_Form_Control_Html('IBAN', $aliasForDisplay);
			$ibanElement = new Customweb_Form_Element(Customweb_I18n_Translation::__('IBAN'), $ibanControl);
			
			$aliasControl = new Customweb_Form_Control_HiddenInput('ALIAS', $alias);
			$aliasElement = new Customweb_Form_HiddenElement($aliasControl);
			
			$additionalData = array(
				$ibanElement,
				$aliasElement 
			);
		}
		else {
			$builder->setIbanFieldName('IBAN');
		}
		
		return array_merge($builder->build(), $additionalData);
	}

	public function getAuthorizationParameters(Customweb_Barclaycard_Authorization_Transaction $transaction, array $formData, $authorizationMethod){
		$parameters = parent::getAuthorizationParameters($transaction, $formData, $authorizationMethod);
		if (!isset($formData['ALIAS']) && $authorizationMethod !== Customweb_Payment_Authorization_Recurring_IAdapter::AUTHORIZATION_METHOD_NAME) {
			if (!isset($formData['IBAN']) || empty($formData['IBAN'])) {
				throw new Customweb_Payment_Exception_PaymentErrorException(
						new Customweb_Payment_Authorization_ErrorMessage(Customweb_I18n_Translation::__("No IBAN provided.")));
			}
			$iban = $formData['IBAN'];
			$handler = new Customweb_Payment_Authorization_Method_Sepa_Iban();
			$iban = $handler->sanitize($iban);
			$handler->validate($iban);
			
			$mandateId = Customweb_Payment_Authorization_Method_Sepa_Mandate::getMandateIdFromCustomerContext(
					$transaction->getTransactionContext()->getPaymentCustomerContext(), $this);
			// 		Customweb_Payment_Authorization_Method_Sepa_Mandate::resetMandateId($transaction->getTransactionContext()->getPaymentCustomerContext(), $this);
			

			$parameters['CARDNO'] = $iban;
			$parameters['MANDATEID'] = $mandateId;
			$parameters['SIGNDATE'] = Customweb_Core_DateTime::_()->format("Ymd");
		}
		if($transaction->getTransactionContext()->createRecurringAlias()){
			$parameters['SEQUENCETYPE'] = 'FRST';
		}
		if($transaction->getTransactionContext()->getAlias() == 'new'){
			$parameters['SEQUENCETYPE'] = 'FRST';
		}
		else if($transaction->getTransactionContext()->getAlias() != null){
			$parameters['SEQUENCETYPE'] = 'RCUR';
			$aliasTransaction = $transaction->getTransactionContext()->getAlias();
			if($aliasTransaction !== null){
				$initialParameters = $aliasTransaction->getAuthorizationParameters();
				if(isset($initialParameters['MANDATEID'])){
					$parameters['MANDATEID'] = $initialParameters['MANDATEID'];
				}
				if(isset($initialParameters['SIGNDATE'])){
					$parameters['SIGNDATE'] = $initialParameters['SIGNDATE'];
				}
			}
		}
		if($authorizationMethod == Customweb_Payment_Authorization_Recurring_IAdapter::AUTHORIZATION_METHOD_NAME){
			$parameters['SEQUENCETYPE'] = 'RCUR';
			if(	$transaction->getTransactionContext() instanceof Customweb_Payment_Authorization_Recurring_ITransactionContext){
				$initialTransaction = $transaction->getTransactionContext()->getInitialTransaction();
				if($initialTransaction !== null){
					$initialParameters = $initialTransaction->getAuthorizationParameters();
					if(isset($initialParameters['MANDATEID'])){
						$parameters['MANDATEID'] = $initialParameters['MANDATEID'];
					}
					if(isset($initialParameters['SIGNDATE'])){
						$parameters['SIGNDATE'] = $initialParameters['SIGNDATE'];
					}
				}
			}
		}
		
		return $parameters;
	}
}