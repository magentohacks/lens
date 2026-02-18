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
 * @Method(paymentMethods={'swissbilling'})
 */
class Customweb_Barclaycard_Method_SwissBilling extends Customweb_Barclaycard_Method_DefaultMethod {

	public function preValidate(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext){
		parent::preValidate($orderContext, $paymentContext);
		$this->validate($orderContext, $paymentContext);
	}

	public function validate(Customweb_Payment_Authorization_IOrderContext $orderContext, Customweb_Payment_Authorization_IPaymentCustomerContext $paymentContext){
		if (!Customweb_Util_Address::compareAddresses($orderContext->getBillingAddress(), $orderContext->getShippingAddress())) {
			throw new Exception(Customweb_I18n_Translation::__("Your addresses must be the same to use swissbilling."));
		}
		if (!$this->isCustomerTypeSupported('b2b') && !$this->isCustomerTypeSupported('b2c')) {
			throw new Exception(Customweb_I18n_Translation::__("Either B2B or B2C customers must be active."));
		}
	}

	public function getAuthorizationParameters(Customweb_Barclaycard_Authorization_Transaction $transaction, array $formData, $authorizationMethod){
		$this->validate($transaction->getTransactionContext()->getOrderContext(), $transaction->getPaymentCustomerContext());
		
		$parameters = parent::getAuthorizationParameters($transaction, $formData, $authorizationMethod);
		
		if (isset($formData['barclaycard-b2b']) && $this->isCustomerTypeSupported('b2b')) {
			$company = trim($transaction->getTransactionContext()->getOrderContext()->getBillingAddress()->getCompanyName());
			if (empty($company)) {
				$company = trim($formData['barclaycard-b2b']);
			}
			if (empty($company)) {
				throw new Exception(Customweb_I18n_Translation::__("Please enter your company name."));
			}
			// addresses must be equal.
			$parameters['ECOM_BILLTO_COMPANY'] = $company;
			$parameters['ECOM_SHIPTO_COMPANY'] = $company;
		}
		
		$lineItemBuilder = new Customweb_Barclaycard_Method_LineItemBuilder_SwissBilling(
				$transaction->getTransactionContext()->getOrderContext(), $this->getItemCategory());
		$parameters = array_merge($parameters, $lineItemBuilder->build());
		
		return $parameters;
	}

	public function filterAuthorizationParameters(array $parameters){
		$street = Customweb_Util_Address::splitStreet($parameters['ECOM_BILLTO_POSTAL_STREET_LINE1'], $parameters['ECOM_BILLTO_POSTAL_COUNTRYCODE'],
				$parameters['ECOM_BILLTO_POSTAL_POSTALCODE']);
		// addresses must be equal.
		$parameters['ECOM_BILLTO_POSTAL_STREET_LINE1'] = $street['street'];
		$parameters['ECOM_BILLTO_POSTAL_STREET_NUMBER'] = $street['street-number'];
		$parameters['ECOM_SHIPTO_POSTAL_STREET_LINE1'] = $street['street'];
		$parameters['ECOM_SHIPTO_POSTAL_STREET_NUMBER'] = $street['street-number'];
		
		unset($parameters['CN']);
		unset($parameters['OWNERZIP']);
		unset($parameters['OWNERADDRESS']);
		unset($parameters['OWNERTOWN']);
		unset($parameters['OWNERCTY']);
		unset($parameters['ECOM_SHIPTO_ONLINE_EMAIL']);
		
		return $parameters;
	}

	private function getItemCategory(){
		$category = $this->getPaymentMethodConfigurationValue('item_category');
		if (empty($category)) {
			throw new Exception(
					Customweb_I18n_Translation::__("You must select a value for the configuration 'Item Category' in the swissbilling configuration."));
		}
		return $category;
	}

	public function getFormFields(Customweb_Payment_Authorization_IOrderContext $orderContext, $aliasTransaction, $failedTransaction, $authorizationMethod, $isMoto, $customerPaymentContext){
		$fields = parent::getFormFields($orderContext, $aliasTransaction, $failedTransaction, $authorizationMethod, $isMoto, $customerPaymentContext);
		if ($this->isCustomerTypeSupported('b2b')) {
			if (!$this->isCustomerTypeSupported('b2c')) {
				if (trim($orderContext->getBillingAddress()->getCompanyName()) == null) { // b2b && !b2c company=null
					$companyControl = new Customweb_Form_Control_TextInput('barclaycard-b2b');
					$companyControl->addValidator(
							new Customweb_Form_Validator_NotEmpty($companyControl, Customweb_I18n_Translation::__("Please enter your company name.")));
					$fields[] = new Customweb_Form_Element(Customweb_I18n_Translation::__("Company"), $companyControl);
				}
				else { // b2b && !b2c, company=set
					$companyControl = new Customweb_Form_Control_HiddenInput('barclaycard-b2b', 'active');
					$fields[] = new Customweb_Form_HiddenElement($companyControl);
				}
			}
			else {
				if (trim($orderContext->getBillingAddress()->getCompanyName()) != null) { // b2b && b2c, company=set
					$companyControl = new Customweb_Form_Control_SingleCheckbox('barclaycard-b2b', 'active',
							Customweb_I18n_Translation::__("You may wish to process the transaction as B2B for different rates and conditions."), true);
					$companyControl->setRequired(false);
					$companyField = new Customweb_Form_Element(Customweb_I18n_Translation::__("B2B transaction"), $companyControl);
					$companyField->setRequired(false);
				}
				// b2b && b2c, company=null => b2b
			}
		}
		// !b2b && b2c, company=whocares => b2c
		return $fields;
	}

	private function isCustomerTypeSupported($customerType){
		return in_array($customerType, $this->getPaymentMethodConfigurationValue('customer_type'));
	}

	private function isB2B(){
		return $this->getPaymentMethodConfigurationValue('customer_type') == 'b2b';
	}
}	