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

define([
	'uiComponent',
	'Magento_Checkout/js/model/payment/renderer-list'
], function(
	Component,
	rendererList
) {
	'use strict';
	
	rendererList.push(
			{
			    type: 'barclaycardcw_mastercard',
			    component: 'Customweb_BarclaycardCw/js/view/payment/method-renderer/barclaycardcw_mastercard-method'
			},
			{
			    type: 'barclaycardcw_masterpass',
			    component: 'Customweb_BarclaycardCw/js/view/payment/method-renderer/barclaycardcw_masterpass-method'
			},
			{
			    type: 'barclaycardcw_creditcard',
			    component: 'Customweb_BarclaycardCw/js/view/payment/method-renderer/barclaycardcw_creditcard-method'
			},
			{
			    type: 'barclaycardcw_americanexpress',
			    component: 'Customweb_BarclaycardCw/js/view/payment/method-renderer/barclaycardcw_americanexpress-method'
			},
			{
			    type: 'barclaycardcw_jcb',
			    component: 'Customweb_BarclaycardCw/js/view/payment/method-renderer/barclaycardcw_jcb-method'
			},
			{
			    type: 'barclaycardcw_visa',
			    component: 'Customweb_BarclaycardCw/js/view/payment/method-renderer/barclaycardcw_visa-method'
			},
			{
			    type: 'barclaycardcw_maestro',
			    component: 'Customweb_BarclaycardCw/js/view/payment/method-renderer/barclaycardcw_maestro-method'
			},
			{
			    type: 'barclaycardcw_paypal',
			    component: 'Customweb_BarclaycardCw/js/view/payment/method-renderer/barclaycardcw_paypal-method'
			});
	return Component.extend({});
});