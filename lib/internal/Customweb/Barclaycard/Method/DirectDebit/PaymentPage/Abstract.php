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
 */
abstract class Customweb_Barclaycard_Method_DirectDebit_PaymentPage_Abstract extends Customweb_Barclaycard_Method_DefaultMethod {

	public function getAuthorizationParameters(Customweb_Barclaycard_Authorization_Transaction $transaction, array $formData, $authorizationMethod){
		$parameters = parent::getAuthorizationParameters($transaction, $formData, $authorizationMethod);
		
		$mandateParameters = array();
		$schema = '{year}-{month}-{day}: {random}';
		if ($this->existsPaymentMethodConfigurationValue('sepa_mandate_id_schema')) {
			$schema = $this->getPaymentMethodConfigurationValue('sepa_mandate_id_schema');
		}
		$mandateId = Customweb_Barclaycard_Util::cleanMandateIdExtended(Customweb_Payment_Authorization_Method_Sepa_Mandate::generateMandateId($schema));
		$mandateParameters['MANDATEID'] = $mandateId;
		$mandateParameters['SIGNDATE'] = Customweb_Core_DateTime::_()->format("Ymd");
		
		if ($transaction->getTransactionContext()->createRecurringAlias()) {
			$mandateParameters['SEQUENCETYPE'] = 'FRST';
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
					$mandateParameters['MANDATEID'] = $initialParameters['MANDATEID'];
				}
				if(isset($initialParameters['SIGNDATE'])){
					$mandateParameters['SIGNDATE'] = $initialParameters['SIGNDATE'];
				}
			}
		}
		$transaction->appendAuthorizationParameters($mandateParameters);
		return array_merge($parameters, $mandateParameters);
	}
}