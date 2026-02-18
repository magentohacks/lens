<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Pdfinvoiceplus\Block\Cart;

use Magento\Store\Model\ScopeInterface;

/**
 * Cart sidebar block
 */
class Sidebar extends \Magento\Checkout\Block\Cart\Sidebar{

    /**
     * Xml pah to display print quote button
     */
    const XML_PATH_PDF_PRINT_QUOTE = 'pdfinvoiceplus/general/show_print_quote';
    /**
     * Returns minicart config
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'shoppingCartUrl' => $this->getShoppingCartUrl(),
            'checkoutUrl' => $this->getCheckoutUrl(),
            'updateItemQtyUrl' => $this->getUpdateItemQtyUrl(),
            'removeItemUrl' => $this->getRemoveItemUrl(),
            'imageTemplate' => $this->getImageHtmlTemplate(),
            'baseUrl' => $this->getBaseUrl(),
            'minicartMaxItemsVisible' => $this->getMiniCartMaxItemsCount(),
            'websiteId' => $this->_storeManager->getStore()->getWebsiteId(),
            'printQuoteUrl' => $this->getUrl('pdfinvoiceplus/printPdf/quote'),
            'displayButton' => (bool)$this->_scopeConfig->getValue(self::XML_PATH_PDF_PRINT_QUOTE,ScopeInterface::SCOPE_STORE)
        ];
    }
}