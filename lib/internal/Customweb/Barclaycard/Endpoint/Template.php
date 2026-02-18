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
 * @Controller("template")
 *
 */
class Customweb_Barclaycard_Endpoint_Template extends Customweb_Payment_Endpoint_Controller_Abstract {

	/**
	 * @Action("index")
	 */
	public function index(Customweb_Core_Http_IRequest $request){
		$templateContext = new Customweb_Mvc_Template_RenderContext();
		$templateContext->setSecurityPolicy(new Customweb_Mvc_Template_SecurityPolicy());
		$templateContext->setTemplate('template');
		$content = $this->getTemplateRenderer()->render($templateContext);
		
		$layoutContext = new Customweb_Mvc_Layout_RenderContext();
		$layoutContext->setTitle('Payment');
		$layoutContext->setMainContent($content);
		$completeTemplate = $this->getLayoutRenderer()->render($layoutContext);
		
		return Customweb_Core_String::_($completeTemplate)->replaceNonAsciiCharsWithEntities()->toString();
	}
}