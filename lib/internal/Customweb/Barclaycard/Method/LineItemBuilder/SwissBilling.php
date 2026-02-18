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
 */
class Customweb_Barclaycard_Method_LineItemBuilder_SwissBilling extends Customweb_Barclaycard_AbstractLineItemBuilder {
	private $category;

	public function __construct(Customweb_Payment_Authorization_IOrderContext $orderContext, $category){
		parent::__construct($orderContext);
		$this->category = Customweb_Core_String::_($category)->substring(0, 50)->toString();
	}

	protected function getLineItemFields(Customweb_Payment_Authorization_IInvoiceItem $item, $counter){
		$fields = array();
		$fields['ITEMID'] = $counter;
		$fields['ITEMNAME'] = Customweb_Barclaycard_Util::substrUtf8($this->sanatizeItemName($item->getName()), 0, 40);
		$fields['ITEMPRICE'] = $this->getProductPriceIncludingTax($item);
		$fields['ITEMQUANT'] = $item->getQuantity();
		$fields['ITEMVATCODE'] = round($item->getTaxRate(), 2) . "%";
		$fields['TAXINCLUDED'] = 1;
		$fields['ITEMCATEGORY'] = $this->getItemCategory();
		
		return $fields;
	}

	protected function getAllowedProductTypes(){
		return array(
			Customweb_Payment_Authorization_IInvoiceItem::TYPE_SHIPPING,
			Customweb_Payment_Authorization_IInvoiceItem::TYPE_FEE,
			Customweb_Payment_Authorization_IInvoiceItem::TYPE_PRODUCT 
		);
	}

	protected function getItemCategory(){
		return $this->category;
	}
}
