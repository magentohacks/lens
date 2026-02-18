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
 */



/**
 *
 * @author Thomas Hunziker
 * @Method(paymentMethods={'Paysafecard'})
 */
class Customweb_Barclaycard_Method_PaySafeCard extends Customweb_Barclaycard_Method_DefaultMethod {
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see Customweb_Barclaycard_Method_DefaultMethod::getAuthorizationParameters()
	 */
	public function getAuthorizationParameters(Customweb_Barclaycard_Authorization_Transaction $transaction, array $formData, $authorizationMethod){
		$parameters = parent::getAuthorizationParameters($transaction, $formData, $authorizationMethod);
		
		$customerId = $transaction->getTransactionContext()->getOrderContext()->getCustomerId();
		if(empty($customerId)){
			$customerId = hash("sha256", $transaction->getTransactionContext()->getOrderContext()->getCustomerEMailAddress());
		}
		$parameters['REF_CUSTOMERID'] = Customweb_Util_String::substrUtf8($customerId, 0, 20);
		return $parameters;
	}
	
}