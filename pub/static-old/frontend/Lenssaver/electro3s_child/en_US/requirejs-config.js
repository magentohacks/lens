(function(require){
(function() {
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */

var config = {
    map: {
        '*': {
            'nonceInjector': 'Magento_Csp/js/nonce-injector'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            directoryRegionUpdater: 'Magento_Directory/js/region-updater'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    waitSeconds: 0,
    map: {
        '*': {
            'ko': 'knockoutjs/knockout',
            'knockout': 'knockoutjs/knockout',
            'mageUtils': 'mage/utils/main',
            'rjsResolver': 'mage/requirejs/resolver',
            'jquery-ui-modules/core': 'jquery/ui-modules/core',
            'jquery-ui-modules/accordion': 'jquery/ui-modules/widgets/accordion',
            'jquery-ui-modules/autocomplete': 'jquery/ui-modules/widgets/autocomplete',
            'jquery-ui-modules/button': 'jquery/ui-modules/widgets/button',
            'jquery-ui-modules/datepicker': 'jquery/ui-modules/widgets/datepicker',
            'jquery-ui-modules/dialog': 'jquery/ui-modules/widgets/dialog',
            'jquery-ui-modules/draggable': 'jquery/ui-modules/widgets/draggable',
            'jquery-ui-modules/droppable': 'jquery/ui-modules/widgets/droppable',
            'jquery-ui-modules/effect-blind': 'jquery/ui-modules/effects/effect-blind',
            'jquery-ui-modules/effect-bounce': 'jquery/ui-modules/effects/effect-bounce',
            'jquery-ui-modules/effect-clip': 'jquery/ui-modules/effects/effect-clip',
            'jquery-ui-modules/effect-drop': 'jquery/ui-modules/effects/effect-drop',
            'jquery-ui-modules/effect-explode': 'jquery/ui-modules/effects/effect-explode',
            'jquery-ui-modules/effect-fade': 'jquery/ui-modules/effects/effect-fade',
            'jquery-ui-modules/effect-fold': 'jquery/ui-modules/effects/effect-fold',
            'jquery-ui-modules/effect-highlight': 'jquery/ui-modules/effects/effect-highlight',
            'jquery-ui-modules/effect-scale': 'jquery/ui-modules/effects/effect-scale',
            'jquery-ui-modules/effect-pulsate': 'jquery/ui-modules/effects/effect-pulsate',
            'jquery-ui-modules/effect-shake': 'jquery/ui-modules/effects/effect-shake',
            'jquery-ui-modules/effect-slide': 'jquery/ui-modules/effects/effect-slide',
            'jquery-ui-modules/effect-transfer': 'jquery/ui-modules/effects/effect-transfer',
            'jquery-ui-modules/effect': 'jquery/ui-modules/effect',
            'jquery-ui-modules/menu': 'jquery/ui-modules/widgets/menu',
            'jquery-ui-modules/mouse': 'jquery/ui-modules/widgets/mouse',
            'jquery-ui-modules/position': 'jquery/ui-modules/position',
            'jquery-ui-modules/progressbar': 'jquery/ui-modules/widgets/progressbar',
            'jquery-ui-modules/resizable': 'jquery/ui-modules/widgets/resizable',
            'jquery-ui-modules/selectable': 'jquery/ui-modules/widgets/selectable',
            'jquery-ui-modules/selectmenu': 'jquery/ui-modules/widgets/selectmenu',
            'jquery-ui-modules/slider': 'jquery/ui-modules/widgets/slider',
            'jquery-ui-modules/sortable': 'jquery/ui-modules/widgets/sortable',
            'jquery-ui-modules/spinner': 'jquery/ui-modules/widgets/spinner',
            'jquery-ui-modules/tabs': 'jquery/ui-modules/widgets/tabs',
            'jquery-ui-modules/tooltip': 'jquery/ui-modules/widgets/tooltip',
            'jquery-ui-modules/widget': 'jquery/ui-modules/widget',
            'jquery-ui-modules/timepicker': 'jquery/timepicker',
            'vimeo': 'vimeo/player',
            'vimeoWrapper': 'vimeo/vimeo-wrapper'
        }
    },
    shim: {
        'mage/adminhtml/backup': ['prototype'],
        'mage/captcha': ['prototype'],
        'mage/new-gallery': ['jquery'],
        'jquery/ui': ['jquery'],
        'matchMedia': {
            'exports': 'mediaCheck'
        },
        'magnifier/magnifier': ['jquery'],
        'vimeo/player': {
            'exports': 'Player'
        }
    },
    paths: {
        'jquery/validate': 'jquery/jquery.validate',
        'jquery/uppy-core': 'jquery/uppy/dist/uppy.min',
        'prototype': 'legacy-build.min',
        'jquery/jquery-storageapi': 'js-storage/storage-wrapper',
        'text': 'mage/requirejs/text',
        'domReady': 'requirejs/domReady',
        'spectrum': 'jquery/spectrum/spectrum',
        'tinycolor': 'jquery/spectrum/tinycolor',
        'jquery-ui-modules': 'jquery/ui-modules'
    },
    config: {
        text: {
            'headers': {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }
    }
};

require(['jquery'], function ($) {
    'use strict';

    $.noConflict();
});

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'rowBuilder':             'Magento_Theme/js/row-builder',
            'toggleAdvanced':         'mage/toggle',
            'translateInline':        'mage/translate-inline',
            'sticky':                 'mage/sticky',
            'tabs':                   'mage/tabs',
            'collapsible':            'mage/collapsible',
            'dropdownDialog':         'mage/dropdown',
            'dropdown':               'mage/dropdowns',
            'accordion':              'mage/accordion',
            'loader':                 'mage/loader',
            'tooltip':                'mage/tooltip',
            'deletableItem':          'mage/deletable-item',
            'itemTable':              'mage/item-table',
            'fieldsetControls':       'mage/fieldset-controls',
            'fieldsetResetControl':   'mage/fieldset-controls',
            'redirectUrl':            'mage/redirect-url',
            'loaderAjax':             'mage/loader',
            'menu':                   'mage/menu',
            'popupWindow':            'mage/popup-window',
            'validation':             'mage/validation/validation',
            'breadcrumbs':            'Magento_Theme/js/view/breadcrumbs',
            'jquery/ui':              'jquery/compat',
            'cookieStatus':           'Magento_Theme/js/cookie-status'
        }
    },
    deps: [
        'mage/common',
        'mage/dataPost',
        'mage/bootstrap'
    ],
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Magento_Theme/js/view/add-home-breadcrumb': true
            }
        }
    }
};

/* eslint-disable max-depth */
/**
 * Adds polyfills only for browser contexts which prevents bundlers from including them.
 */
if (typeof window !== 'undefined' && window.document) {
    /**
     * Polyfill localStorage and sessionStorage for browsers that do not support them.
     */
    try {
        if (!window.localStorage || !window.sessionStorage) {
            throw new Error();
        }

        localStorage.setItem('storage_test', 1);
        localStorage.removeItem('storage_test');
    } catch (e) {
        config.deps.push('mage/polyfill');
    }
}
/* eslint-enable max-depth */

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            escaper: 'Magento_Security/js/escaper'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            checkoutBalance:    'Magento_Customer/js/checkout-balance',
            address:            'Magento_Customer/js/address',
            changeEmailPassword: 'Magento_Customer/js/change-email-password',
            passwordStrengthIndicator: 'Magento_Customer/js/password-strength-indicator',
            zxcvbn: 'Magento_Customer/js/zxcvbn',
            addressValidation: 'Magento_Customer/js/addressValidation',
            showPassword: 'Magento_Customer/js/show-password',
            'Magento_Customer/address': 'Magento_Customer/js/address',
            'Magento_Customer/change-email-password': 'Magento_Customer/js/change-email-password',
            globalSessionLoader:    'Magento_Customer/js/customer-global-session-loader.js'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            quickSearch: 'Magento_Search/js/form-mini',
            'Magento_Search/form-mini': 'Magento_Search/js/form-mini'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            priceBox:             'Magento_Catalog/js/price-box',
            priceOptionDate:      'Magento_Catalog/js/price-option-date',
            priceOptionFile:      'Magento_Catalog/js/price-option-file',
            priceOptions:         'Magento_Catalog/js/price-options',
            priceUtils:           'Magento_Catalog/js/price-utils'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            compareList:            'Magento_Catalog/js/list',
            relatedProducts:        'Magento_Catalog/js/related-products',
            upsellProducts:         'Magento_Catalog/js/upsell-products',
            productListToolbarForm: 'Magento_Catalog/js/product/list/toolbar',
            catalogGallery:         'Magento_Catalog/js/gallery',
            catalogAddToCart:       'Magento_Catalog/js/catalog-add-to-cart'
        }
    },
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Magento_Catalog/js/product/breadcrumbs': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            addToCart: 'Magento_Msrp/js/msrp'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            catalogSearch: 'Magento_CatalogSearch/form-mini'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            creditCardType: 'Magento_Payment/js/cc-type',
            'Magento_Payment/cc-type': 'Magento_Payment/js/cc-type'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftMessage:    'Magento_Sales/js/gift-message',
            ordersReturns:  'Magento_Sales/js/orders-returns',
            'Magento_Sales/gift-message':    'Magento_Sales/js/gift-message',
            'Magento_Sales/orders-returns':  'Magento_Sales/js/orders-returns'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            discountCode:           'Magento_Checkout/js/discount-codes',
            shoppingCart:           'Magento_Checkout/js/shopping-cart',
            regionUpdater:          'Magento_Checkout/js/region-updater',
            sidebar:                'Magento_Checkout/js/sidebar',
            checkoutLoader:         'Magento_Checkout/js/checkout-loader',
            checkoutData:           'Magento_Checkout/js/checkout-data',
            proceedToCheckout:      'Magento_Checkout/js/proceed-to-checkout',
            catalogAddToCart:       'Magento_Catalog/js/catalog-add-to-cart'
        }
    },
    shim: {
        'Magento_Checkout/js/model/totals' : {
            deps: ['Magento_Customer/js/customer-data']
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            requireCookie: 'Magento_Cookie/js/require-cookie',
            cookieNotices: 'Magento_Cookie/js/notices'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            downloadable: 'Magento_Downloadable/js/downloadable',
            'Magento_Downloadable/downloadable': 'Magento_Downloadable/js/downloadable'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            subscriptionStatusResolver: 'Magento_Newsletter/js/subscription-status-resolver',
            newsletterSignUp:  'Magento_Newsletter/js/newsletter-sign-up'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            bundleOption:   'Magento_Bundle/bundle',
            priceBundle:    'Magento_Bundle/js/price-bundle',
            slide:          'Magento_Bundle/js/slide',
            productSummary: 'Magento_Bundle/js/product-summary'
        }
    },
    config: {
        mixins: {
            'mage/validation': {
                'Magento_Bundle/js/validation': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    deps: [],
    shim: {
        'chartjs/chartjs-adapter-moment': ['moment'],
        'chartjs/es6-shim.min': {},
        'hugerte/hugerte.min': {
            exports: 'hugerte',
            init: function () {
                'use strict';
                window.tinymce = window.hugerte;
                window.tinyMCE = window.hugerte;
                return window.hugerte;
            }
        }
    },
    paths: {
        'ui/template': 'Magento_Ui/templates'
    },
    map: {
        '*': {
            uiElement:      'Magento_Ui/js/lib/core/element/element',
            uiCollection:   'Magento_Ui/js/lib/core/collection',
            uiComponent:    'Magento_Ui/js/lib/core/collection',
            uiClass:        'Magento_Ui/js/lib/core/class',
            uiEvents:       'Magento_Ui/js/lib/core/events',
            uiRegistry:     'Magento_Ui/js/lib/registry/registry',
            consoleLogger:  'Magento_Ui/js/lib/logger/console-logger',
            uiLayout:       'Magento_Ui/js/core/renderer/layout',
            buttonAdapter:  'Magento_Ui/js/form/button-adapter',
            chartJs:        'chartjs/Chart.min',
            'chart.js':     'chartjs/Chart.min',
            tinymce:        'hugerte/hugerte.min',
            wysiwygAdapter: 'mage/adminhtml/wysiwyg/tiny_mce/tinymceAdapter'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    deps: [
        'Magento_Ui/js/core/app'
    ]
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            pageCache:  'Magento_PageCache/js/page-cache'
        }
    },
    deps: ['Magento_PageCache/js/form-key-provider']
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            groupedProduct: 'Magento_GroupedProduct/js/grouped-product'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            captcha: 'Magento_Captcha/js/captcha',
            'Magento_Captcha/captcha': 'Magento_Captcha/js/captcha'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            configurable: 'Magento_ConfigurableProduct/js/configurable'
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'Magento_ConfigurableProduct/js/catalog-add-to-cart-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            multiShipping: 'Magento_Multishipping/js/multi-shipping',
            orderOverview: 'Magento_Multishipping/js/overview',
            payment: 'Magento_Multishipping/js/payment',
            billingLoader: 'Magento_Checkout/js/checkout-loader',
            cartUpdate: 'Magento_Checkout/js/action/update-shopping-cart',
            multiShippingBalance: 'Magento_Multishipping/js/multi-shipping-balance'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            recentlyViewedProducts: 'Magento_Reports/js/recently-viewed'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/quote': {
                'Magento_InventoryInStorePickupFrontend/js/model/quote-ext': true
            },
            'Magento_Checkout/js/view/shipping-information': {
                'Magento_InventoryInStorePickupFrontend/js/view/shipping-information-ext': true
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Magento_InventoryInStorePickupFrontend/js/model/checkout-data-resolver-ext': true
            },
            'Magento_Checkout/js/checkout-data': {
                'Magento_InventoryInStorePickupFrontend/js/checkout-data-ext': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Magento_InventorySwatchesFrontendUi/js/swatch-renderer': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/select-payment-method': {
                'Magento_SalesRule/js/action/select-payment-method-mixin': true
            },
            'Magento_Checkout/js/model/shipping-save-processor': {
                'Magento_SalesRule/js/model/shipping-save-processor-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Magento_SalesRule/js/model/place-order-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright 2023 Adobe
 * All Rights Reserved.
 */
var config = {
    map: {
        '*': {
            'cancelOrderModal': 'Magento_OrderCancellationUi/js/cancel-order-modal'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'slick': 'Magento_PageBuilder/js/resource/slick/slick',
            'jarallax': 'Magento_PageBuilder/js/resource/jarallax/jarallax',
            'jarallaxVideo': 'Magento_PageBuilder/js/resource/jarallax/jarallax-video',
            'Magento_PageBuilder/js/resource/vimeo/player': 'vimeo/player',
            'Magento_PageBuilder/js/resource/vimeo/vimeo-wrapper': 'vimeo/vimeo-wrapper',
            'jarallax-wrapper': 'Magento_PageBuilder/js/resource/jarallax/jarallax-wrapper'
        }
    },
    shim: {
        'Magento_PageBuilder/js/resource/slick/slick': {
            deps: ['jquery']
        },
        'Magento_PageBuilder/js/resource/jarallax/jarallax-video': {
            deps: ['jarallax-wrapper', 'vimeoWrapper']
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    shim: {
        cardinaljs: {
            exports: 'Cardinal'
        },
        cardinaljsSandbox: {
            exports: 'Cardinal'
        }
    },
    paths: {
        cardinaljsSandbox: 'https://includestest.ccdc02.com/cardinalcruise/v1/songbird',
        cardinaljs: 'https://songbird.cardinalcommerce.com/edge/v1/songbird'
    }
};


require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            transparent: 'Magento_Payment/js/transparent',
            'Magento_Payment/transparent': 'Magento_Payment/js/transparent'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            orderReview: 'Magento_Paypal/js/order-review',
            'Magento_Paypal/order-review': 'Magento_Paypal/js/order-review',
            paypalCheckout: 'Magento_Paypal/js/paypal-checkout'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Customer/js/customer-data': {
                'Magento_Persistent/js/view/customer-data-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            loadPlayer: 'Magento_ProductVideo/js/load-player',
            fotoramaVideoEvents: 'Magento_ProductVideo/js/fotorama-add-video-events',
            'vimeoWrapper': 'vimeo/vimeo-wrapper'
        }
    },
    shim: {
        vimeoAPI: {},
        'Magento_ProductVideo/js/load-player': {
            deps: ['vimeoWrapper']
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Magento_CheckoutAgreements/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Magento_CheckoutAgreements/js/model/set-payment-information-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/place-order': {
                'Magento_ReCaptchaCheckout/js/model/place-order-mixin': true
            },
            'Magento_ReCaptchaWebapiUi/js/webapiReCaptchaRegistry': {
                'Magento_ReCaptchaCheckout/js/webapiReCaptchaRegistry-mixin': true
            }
        }
    }
};


require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/*eslint strict: ["error", "global"]*/

'use strict';

var config = {
    config: {
        mixins: {
            'Magento_Ui/js/view/messages': {
                'Magento_ReCaptchaFrontendUi/js/ui-messages-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'Magento_Paypal/js/view/payment/method-renderer/payflowpro-method': {
                'Magento_ReCaptchaPaypal/js/payflowpro-method-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'jquery': {
                'Magento_ReCaptchaWebapiUi/js/jquery-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            scriptLoader: 'Magento_PaymentServicesPaypal/js/lib/script-loader-wrapper'
        }
    },
    shim: {
        'Magento_PaymentServicesPaypal/js/lib/script-loader': {
            init: function () {
                'use strict';

                return {
                    load: window.paypalLoadScript,
                    loadCustom: window.paypalLoadCustomScript
                };
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * ADOBE CONFIDENTIAL
 *
 * Copyright 2022 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Adobe.
 */

var config = {
    map: {
        '*': {
            'Magento_Vault/js/view/payment/vault': 'Magento_PaymentServicesPaypal/js/view/payment/vault'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/model/payment-service': {
                'Magento_PaymentServicesPaypal/js/model/payment-service-mixin': true
            },
            'Magento_Checkout/js/model/step-navigator': {
                'Magento_PaymentServicesPaypal/js/model/step-navigator-mixin': true
            },
            'Magento_Checkout/js/view/form/element/email': {
                'Magento_PaymentServicesPaypal/js/view/form/element/email-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Magento_PaymentServicesPaypal/js/view/shipping-mixin': true
            },
            'Magento_Checkout/js/view/shipping-information': {
                'Magento_PaymentServicesPaypal/js/view/shipping-information-mixin': true
            }
        }
    },
    paths: {
        fastlane: 'https://www.paypalobjects.com/connect-boba'
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            mageTranslationDictionary: 'Magento_Translation/js/mage-translation-dictionary'
        }
    },
    deps: [
        'mageTranslationDictionary'
    ]
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            editTrigger: 'mage/edit-trigger',
            addClass: 'Magento_Translation/js/add-class',
            'Magento_Translation/add-class': 'Magento_Translation/js/add-class'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            configurableVariationQty: 'Magento_InventoryConfigurableProductFrontendUi/js/configurable-variation-qty'
        }
    },
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'Magento_InventoryConfigurableProductFrontendUi/js/configurable': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/payment/list': {
                'Magento_PaypalCaptcha/js/view/payment/list-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/payflowpro-method': {
                'Magento_PaypalCaptcha/js/view/payment/method-renderer/payflowpro-method-mixin': true
            },
            'Magento_Captcha/js/view/checkout/defaultCaptcha': {
                'Magento_PaypalCaptcha/js/view/checkout/defaultCaptcha-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'taxToggle': 'Magento_Weee/js/tax-toggle',
            'Magento_Weee/tax-toggle': 'Magento_Weee/js/tax-toggle'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Config to pull in all the relevant Braintree JS SDKs
 * @type {
 *  paths: {
 *      braintreePayPalInContextCheckout: string,
 *      braintreePayPalCheckout: string,
 *      braintreeVenmo: string,
 *      braintreeHostedFields: string,
 *      braintreeDataCollector: string,
 *      braintreeThreeDSecure: string,
 *      braintreeGooglePay: string,
 *      braintreeApplePay: string,
 *      braintreeAch: string,
 *      braintreeLpm: string,
 *      googlePayLibrary: string
 * },
 *  map: {
 *      "*": {
 *          braintree: string
 *      }
 *  }
 * }
 */
var config = {
    map: {
        '*': {
            braintree: 'https://js.braintreegateway.com/web/3.112.0/js/client.min.js'
        }
    },

    paths: {
        'braintreePayPalCheckout': 'https://js.braintreegateway.com/web/3.112.0/js/paypal-checkout.min',
        'braintreeHostedFields': 'https://js.braintreegateway.com/web/3.112.0/js/hosted-fields.min',
        'braintreeDataCollector': 'https://js.braintreegateway.com/web/3.112.0/js/data-collector.min',
        'braintreeThreeDSecure': 'https://js.braintreegateway.com/web/3.112.0/js/three-d-secure.min',
        'braintreeApplePay': 'https://js.braintreegateway.com/web/3.112.0/js/apple-pay.min',
        'braintreeGooglePay': 'https://js.braintreegateway.com/web/3.112.0/js/google-payment.min',
        'braintreeVenmo': 'https://js.braintreegateway.com/web/3.112.0/js/venmo.min',
        'braintreeAch': 'https://js.braintreegateway.com/web/3.112.0/js/us-bank-account.min',
        'braintreeLpm': 'https://js.braintreegateway.com/web/3.112.0/js/local-payment.min',
        'googlePayLibrary': 'https://pay.google.com/gp/p/js/pay',
        'braintreePayPalInContextCheckout': 'https://www.paypalobjects.com/api/checkout'
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/step-navigator': {
                'PayPal_Braintree/js/model/step-navigator-mixin': true
            },
            'Magento_Checkout/js/model/place-order': {
                'PayPal_Braintree/js/model/place-order-mixin': true
            },
            'Magento_ReCaptchaWebapiUi/js/webapiReCaptchaRegistry': {
                'PayPal_Braintree/js/reCaptcha/webapiReCaptchaRegistry-mixin': true
            },
            'Magento_CheckoutAgreements/js/view/checkout-agreements': {
                'PayPal_Braintree/js/checkoutAgreements/view/checkout-agreements-mixin': true
            }
        }
    },
    map: {
        '*': {
            braintreeCheckoutPayPalAdapter: 'PayPal_Braintree/js/view/payment/adapter'
        }
    }
};

require.config(config);
})();
(function() {
/* jshint browser:true jquery:true */
var amasty_mixin_enabled = !window.amasty_checkout_disabled,
    config;

config = {
    'map': { '*': {} },
    config: {
        mixins: {
            'Magento_Checkout/js/model/new-customer-address': {
                'Amasty_CheckoutCore/js/model/new-customer-address-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/payment/list': {
                'Amasty_CheckoutCore/js/view/payment/list': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/summary/abstract-total': {
                'Amasty_CheckoutCore/js/view/summary/abstract-total': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/step-navigator': {
                'Amasty_CheckoutCore/js/model/step-navigator-mixin': amasty_mixin_enabled
            },
            'Magento_Paypal/js/action/set-payment-method': {
                'Amasty_CheckoutCore/js/action/set-payment-method-mixin': amasty_mixin_enabled
            },
            'Magento_CheckoutAgreements/js/model/agreements-assigner': {
                'Amasty_CheckoutCore/js/model/agreements-assigner-mixin': amasty_mixin_enabled
            },
            'Magento_CheckoutAgreements/js/view/checkout-agreements': {
                'Amasty_CheckoutCore/js/view/checkout-agreements-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/summary': {
                'Amasty_CheckoutCore/js/view/summary-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/shipping': {
                'Amasty_CheckoutCore/js/view/shipping-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/summary/cart-items': {
                'Amasty_CheckoutCore/js/view/summary/cart-items-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/payment/additional-validators': {
                'Amasty_CheckoutCore/js/model/payment-validators/additional-validators-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/customer-email-validator': {
                'Amasty_CheckoutCore/js/model/customer-email-validator-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Amasty_CheckoutCore/js/model/checkout-data-resolver-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/shipping-rates-validator': {
                'Amasty_CheckoutCore/js/model/shipping-rates-validator-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'Amasty_CheckoutCore/js/action/set-shipping-information-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/full-screen-loader': {
                'Amasty_CheckoutCore/js/model/full-screen-loader-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/shipping-rate-processor/new-address': {
                'Amasty_CheckoutCore/js/model/default-shipping-rate-processor-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/payment': {
                'Amasty_CheckoutCore/js/view/payment-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/payment-service': {
                'Amasty_CheckoutCore/js/model/payment-service-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/address-converter': {
                'Amasty_CheckoutCore/js/model/address-converter-mixin': amasty_mixin_enabled
            },
            'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                'Amasty_CheckoutCore/js/view/payment/method-renderer/in-context/checkout-express-mixin':
                    amasty_mixin_enabled
            },

            // in Magento 2.4 module Magento_Braintree renamed to Paypal_Braintree
            'Magento_Braintree/js/view/payment/method-renderer/paypal': {
                'Amasty_CheckoutCore/js/view/payment/method-renderer/braintree/paypal-mixin':
                    amasty_mixin_enabled
            },
            'PayPal_Braintree/js/view/payment/method-renderer/paypal': {
                'Amasty_CheckoutCore/js/view/payment/method-renderer/braintree/paypal-mixin':
                    amasty_mixin_enabled
            },
            'Magento_Braintree/js/view/payment/method-renderer/cc-form': {
                'Amasty_CheckoutCore/js/view/payment/method-renderer/braintree/cc-form-mixin': amasty_mixin_enabled
            },
            'PayPal_Braintree/js/view/payment/method-renderer/cc-form': {
                'Amasty_CheckoutCore/js/view/payment/method-renderer/braintree/cc-form-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/billing-address': {
                'Amasty_CheckoutCore/js/view/billing-address-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/payment/default': {
                'Amasty_CheckoutCore/js/view/payment/method-renderer/default-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/shipping-rate-registry': {
                'Amasty_CheckoutCore/js/model/shipping-rate-registry-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'Amasty_CheckoutCore/js/view/shipping-address/address-renderer/default-mixin': amasty_mixin_enabled
            },
            'Amasty_Gdpr/js/model/consents-assigner': {
                'Amasty_CheckoutCore/js/model/consents-assigner-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/action/select-payment-method': {
                // Disable hardcoded save payment information
                // @see Amasty_CheckoutCore/js/model/payment/salesrule-observer
                'Magento_SalesRule/js/action/select-payment-method-mixin': !amasty_mixin_enabled
            },
            'Magento_Checkout/js/action/select-shipping-address': {
                'Amasty_CheckoutCore/js/action/select-shipping-address-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/billing-address-postcode-validator': {
                'Amasty_CheckoutCore/js/model/billing-address-postcode-validator-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/model/quote': {
                'Amasty_CheckoutCore/js/model/quote-mixin': amasty_mixin_enabled
            }
        }
    }
};

if (amasty_mixin_enabled) {
    config.map['*'] = {
        checkoutCollapsibleSteps: 'Amasty_CheckoutCore/js/view/checkout/design/collapsible-steps',
        amCheckoutCollapsible: 'Amasty_CheckoutCore/js/checkout-collapsible',
        summaryWidget: 'Amasty_CheckoutCore/js/view/summary/summary-widget',
        stickyWidget: 'Amasty_CheckoutCore/js/view/summary/sticky-widget',
        'Magento_Checkout/template/payment-methods/list.html': 'Amasty_CheckoutCore/template/payment-methods/list.html',
        'Magento_Checkout/template/billing-address/details.html':
            'Amasty_CheckoutCore/template/onepage/billing-address/details.html',
        'Magento_Checkout/js/action/get-totals': 'Amasty_CheckoutCore/js/action/get-totals',
        'Magento_Checkout/js/model/shipping-rate-service': 'Amasty_CheckoutCore/js/model/shipping-rate-service-override',
        'Magento_Checkout/js/action/recollect-shipping-rates': 'Amasty_CheckoutCore/js/action/recollect-shipping-rates',
        'Magento_InventoryInStorePickupFrontend/js/model/quote-ext': 'Amasty_CheckoutCore/js/model/quote-ext-override'
    };
}

require.config(config);
})();
(function() {
/* jshint browser:true jquery:true */
var amasty_mixin_enabled = !window.amasty_checkout_disabled,
    config;

config = {
    config: {
        mixins: {
            'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                'Amasty_CheckoutStyleSwitcher/js/view/payment/checkout-express-mixin': amasty_mixin_enabled
            }
        }
    }
};

require.config(config);
})();
(function() {
/* eslint-disable camelcase */
var amasty_mixin_enabled = !window.amasty_checkout_disabled,
    config;

config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/billing-address': {
                'Amasty_Checkout/js/view/billing-address-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/shipping': {
                'Amasty_Checkout/js/view/shipping-mixin': amasty_mixin_enabled
            }
        }
    },
    shim: {
        'Amasty_CheckoutCore/js/view/onepage': [
            'Amasty_Checkout/js/validation/phone-validation'
        ]
    }
};

require.config(config);
})();
(function() {
/* jshint browser:true jquery:true */
var config = {
    config: {
        mixins: {
            'mage/calendar': {
                'Amasty_Mage248Fix/js/calendar-fix-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_Catalog/js/price-box': {
                'Amasty_RecurringPayments/js/view/product/update-prices': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    config: {
        mixins: {
            'Magento_GoogleAnalytics/js/google-analytics': {
                'CustomGento_Cookiebot/js/google-analytics-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            customconfigurable: 'Kraftors_Lenspecification/js/customconfigurable'
        }
    }
};

require.config(config);
})();
(function() {

var config = {
    map: {
        '*': {
            lensReorder: 'Lens_Manager/js/reorder',
        }
    }
};
require.config(config);
})();
(function() {
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 var config = {
 	map: {
 		"*": {
 			lofallOwlCarousel: "Lof_All/lib/owl.carousel/owl.carousel.min",
 			lofallBootstrap: "Lof_All/lib/bootstrap/js/bootstrap.min",
 			lofallColorbox: "Lof_All/lib/colorbox/jquery.colorbox.min",
 			lofallFancybox: "Lof_All/lib/fancybox/jquery.fancybox.pack",
 			lofallFancyboxMouseWheel: "Lof_All/lib/fancybox/jquery.mousewheel-3.0.6.pack"
 		}
 	},
 	shim: {
        'Lof_All/lib/bootstrap/js/bootstrap.min': {
            'deps': ['jquery']
        },
        'Lof_All/lib/bootstrap/js/bootstrap': {
            'deps': ['jquery']
        },
        'Lof_All/lib/owl.carousel/owl.carousel': {
            'deps': ['jquery']
        },
        'Lof_All/lib/owl.carousel/owl.carousel.min': {
        	'deps': ['jquery']
        },
        'Lof_All/lib/fancybox/jquery.fancybox': {
            'deps': ['jquery']  
        },
        'Lof_All/lib/fancybox/jquery.fancybox.pack': {
            'deps': ['jquery']  
        },
        'Lof_All/lib/colorbox/jquery.colorbox': {
            'deps': ['jquery']  
        },
        'Lof_All/lib/colorbox/jquery.colorbox.min': {
            'deps': ['jquery']  
        }
    }
 };
require.config(config);
})();
(function() {
var config = {
    paths: {
        "jquery.cookie": "Magecomp_Cookiecompliance/js/jquery.cookie"
    }
};

require.config(config);
})();
(function() {
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Core
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

var config = {
    paths: {
        'jquery/file-uploader': 'Mageplaza_Core/lib/fileUploader/jquery.fileuploader',
        'mageplaza/core/jquery/popup': 'Mageplaza_Core/js/jquery.magnific-popup.min',
        'mageplaza/core/owl.carousel': 'Mageplaza_Core/js/owl.carousel.min',
        'mageplaza/core/bootstrap': 'Mageplaza_Core/js/bootstrap.min',
        mpIonRangeSlider: 'Mageplaza_Core/js/ion.rangeSlider.min',
        touchPunch: 'Mageplaza_Core/js/jquery.ui.touch-punch.min',
        mpDevbridgeAutocomplete: 'Mageplaza_Core/js/jquery.autocomplete.min'
    },
    shim: {
        "mageplaza/core/jquery/popup": ["jquery"],
        "mageplaza/core/owl.carousel": ["jquery"],
        "mageplaza/core/bootstrap": ["jquery"],
        mpIonRangeSlider: ["jquery"],
        mpDevbridgeAutocomplete: ["jquery"],
        touchPunch: ['jquery', 'jquery-ui-modules/core', 'jquery-ui-modules/mouse', 'jquery-ui-modules/widget']
    }
};

require.config(config);
})();
(function() {
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Smtp
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
var config = {};
if (typeof window.AVADA_EM !== 'undefined') {
    config = {
        config: {
            mixins: {
                'Magento_Checkout/js/view/billing-address': {
                    'Mageplaza_Smtp/js/view/billing-address-mixins' : true
                },
                'Magento_Checkout/js/view/shipping': {
                    'Mageplaza_Smtp/js/view/shipping-mixins' : true
                }
            }
        }
    };
}

require.config(config);
})();
(function() {
var config = {
	map: {
        '*': {
            magepowAjaxcart: 'Magepow_Ajaxcart/js/ajax',
            magepowPopup: 'Magepow_Ajaxcart/js/popup',
            magepowGoto: 'Magepow_Ajaxcart/js/goto',
            magepowProductSuggest: 'Magepow_Ajaxcart/js/suggest'
        }
    },
    config:{
    	mixins: {
         'Magento_ConfigurableProduct/js/configurable': {
               'Magepow_Ajaxcart/js/mixin/configurable': true
           }
      }
  }
};
require.config(config);
})();
(function() {
var config = {
    paths: {
        'magepow/gdpr'  : 'Magepow_Gdpr/js/gdpr'
    },
    shim: {
        'magepow/gdpr': {
            deps: ['jquery']
        }
    }
};
require.config(config);
})();
(function() {

var config = {
	map: {
        '*': {
            magepowInfinitescroll: 'Magepow_InfiniteScroll/js/infinite-scroll'
        }
    }
};
require.config(config);
})();
(function() {


var config = {};
if (window.location.href.indexOf('onestepcheckout') !== -1) {
    config = {
        map: {
            '*':
                {
                'Magento_Checkout/js/model/shipping-rate-service': 'Magepow_OnestepCheckout/js/model/shipping/shipping-rate-service',
                'Magento_Checkout/js/model/shipping-rates-validator': 'Magepow_OnestepCheckout/js/model/shipping/shipping-rates-validator',
                'Magento_CheckoutAgreements/js/model/agreements-assigner': 'Magepow_OnestepCheckout/js/model/agreement/agreements-assigner',
                'Magento_Checkout/js/action/select-payment-method':'Magepow_OnestepCheckout/js/action/select-payment-method'
            },
            'Magepow_OnestepCheckout/js/model/shipping/shipping-rates-validator': {
                'Magento_Checkout/js/model/shipping-rates-validator': 'Magento_Checkout/js/model/shipping-rates-validator'
            },
            'Magento_Checkout/js/model/shipping-save-processor/default': {
                'Magento_Checkout/js/model/full-screen-loader': 'Magepow_OnestepCheckout/js/model/one-step-checkout-loader'
            },
            'Magento_Checkout/js/action/set-billing-address': {
                'Magento_Checkout/js/model/full-screen-loader': 'Magepow_OnestepCheckout/js/model/one-step-checkout-loader'
            },
            'Magento_SalesRule/js/action/set-coupon-code': {
                'Magento_Checkout/js/model/full-screen-loader': 'Magepow_OnestepCheckout/js/model/onestepcheckout-loader/discount'
            },
            'Magento_SalesRule/js/action/cancel-coupon': {
                'Magento_Checkout/js/model/full-screen-loader': 'Magepow_OnestepCheckout/js/model/onestepcheckout-loader/discount'
            },
            'Magepow_OnestepCheckout/js/model/one-step-checkout-loader': {
                'Magento_Checkout/js/model/full-screen-loader': 'Magento_Checkout/js/model/full-screen-loader'
            },

        },
        config: {
            mixins: {
                'Magento_Braintree/js/view/payment/method-renderer/paypal': {
                    'Magepow_OnestepCheckout/js/view/payment/braintree-paypal-mixins': true
                },
                'Magento_Checkout/js/action/place-order': {
                    'Magepow_OnestepCheckout/js/action/place-order-mixins': true
                },
                /*'Magento_Paypal/js/action/set-payment-method': {
                    'Magepow_OnestepCheckout/js/model/set-payment-method-mixin': true
                },
                'Magento_Paypal/js/in-context/express-checkout-wrapper': {
                    'Magepow_OnestepCheckout/js/in-context/express-checkout-wrapper-mixin': true
                },
                'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                    'Magepow_OnestepCheckout/js/view/payment/method-renderer/in-context/checkout-express-mixin': true
                },*/
                'Magento_Paypal/js/action/set-payment-method': {
                    'Magepow_OnestepCheckout/js/action/set-payment-method-mixin': true
                },
                

            }
        }
    };

    if (window.location.href.indexOf('#') !== -1) {
        window.history.pushState("", document.title, window.location.pathname);
    }
}

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            magepowProductzoom: 'Magepow_Productzoom/js/zoom'
        }
    }
};
require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            magepowRecentlyViewed: 'Magepow_RecentlyViewed/js/recently-viewed'
        }
    }
};
require.config(config);
})();
(function() {
var config = {

	paths: {
		'magepow/lazyload'			: 'Magepow_SpeedOptimizer/js/plugin/lazyload',
	}

};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            magepowStickycart: 'Magepow_Stickycart/js/sticky-cart'
        }
    }
};
require.config(config);
})();
(function() {
var config = {

	map: {
		'*': {
			'alothemes'	  : 'magiccart/alothemes',
			'easing'	  : 'magiccart/easing',
			'slick'		  : 'magiccart/slick',
			'gridSlider'  : 'Magiccart_Alothemes/js/grid-slider',
			'gridView'    : 'Magiccart_Alothemes/js/grid-view',
			'notifySlider': 'Magiccart_Alothemes/js/notifyslider'
		},
	},

	paths: {
		'magiccart/easing'			: 'Magiccart_Alothemes/js/plugins/jquery.easing.min',
		'magiccart/parallax'		: 'Magiccart_Alothemes/js/plugins/jquery.parallax',
		'magiccart/socialstream'	: 'Magiccart_Alothemes/js/plugins/jquery.socialstream',
		'magiccart/slick'			: 'Magiccart_Alothemes/js/plugins/slick.min',
		'magiccart/sticky'		    : 'Magiccart_Alothemes/js/plugins/sticky-kit.min',
		'magiccart/wow'				: 'Magiccart_Alothemes/js/plugins/wow.min',
		'magiccart/alothemes'		: 'Magiccart_Alothemes/js/alothemes',
	},

	shim: {
		'magiccart/easing': {
			deps: ['jquery']
		},
		'magiccart/parallax': {
			deps: ['jquery']
		},
		'magiccart/socialstream': {
			deps: ['jquery']
		},
		'magiccart/slick': {
			deps: ['jquery']
		},
		'magiccart/sticky': {
			deps: ['jquery']
		},
		'magiccart/wow': {
			deps: ['jquery']
		},
        'alothemes': {
            deps: ['jquery', 'easing', 'slick']
        },

	}

};

require.config(config);
})();
(function() {
var config = {

	map: {
		'*': {
			'magiccartCampaignbar'    : 'Magiccart_Campaignbar/js/campaign-bar'
		},
	}

};

require.config(config);
})();
(function() {
var config = {

	map: {
		'*': {
			'easing': 'magiccart/easing',
			'easypin': 'magiccart/easypin'
		}
	},

	paths: {
		'magiccart/easing': 'Magiccart_Lookbook/js/plugin/jquery.easing.min',
		'magiccart/easypin': 'Magiccart_Lookbook/js/plugin/jquery.easypin'
	},

	shim: {
		'magiccart/easing': {
			deps: ['jquery']
		},
		'magiccart/easypin': {
			deps: ['jquery', 'easing']
		}
	}

};

require.config(config);
})();
(function() {
var config = {
	map: {
        '*': {
            magiccartLookbook: 'Magiccart_Lookbook/js/look-book'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
	map: {
		'*': {
			'magicmenu': "Magiccart_Magicmenu/js/magicmenu",
		},
	},

	// paths: {
	// 	'magicmenu'	: 'Magiccart_Magicmenu/js/magicmenu',
	// },

	shim: {
		'magicmenu': {
			deps: ['jquery', 'easing']
		},

	}

};

require.config(config);
})();
(function() {
var config = {

	map: {
		'*': {
			'magicproduct'	: "Magiccart_Magicproduct/js/magicproduct",
		},
	},

	shim: {
		'magicproduct': {
			deps: ['jquery', 'slick']
		},

	}
};

require.config(config);
})();
(function() {


var config = {
    map: {
        '*': {
            optionDependent: 'Pektsekye_OptionDependent/main'
        }
    }    
};

require.config(config);
})();
(function() {
/**
 * Solwin Infotech
 * Solwin Discount Coupon Code Link Extension
 *
 * @category   Solwin
 * @package    Solwin_Applycoupon
 * @copyright  Copyright © 2006-2018 Solwin (https://www.solwininfotech.com)
 * @license    https://www.solwininfotech.com/magento-extension-license/ 
 */
var config = {
    map: {
        '*': {
            cpfancybox: 'Solwin_Applycoupon/js/fancybox/jquery.fancybox.pack'
        }
    }
};
require.config(config);
})();
(function() {
/*jshint browser:true jquery:true*/
/*global alert*/
var config = {
    map: {
        '*': {
            'stripejs': 'https://js.stripe.com/v3/',
            'stripe_payments': 'StripeIntegration_Payments/js/stripe_payments'
        }
    }
};

require.config(config);
})();
(function() {
/*jshint browser:true jquery:true*/
/*global alert*/
var config = {
    map: {
        '*': {
            'stripejs': 'https://js.stripe.com/v3/',
            'stripe_payments': 'StripeIntegration_Payments/js/stripe_payments',
            'stripe_payments_express': 'StripeIntegration_Payments/js/stripe_payments_express'
        }
    },
    config: {
        mixins: {
            'Magento_Ui/js/view/messages': {
                'StripeIntegration_Payments/js/mixins/messages-mixin': true
            },
            'Magento_Checkout/js/view/payment/list': {
                'StripeIntegration_Payments/js/mixins/checkout/payment/list': true
            },
            'MSP_ReCaptcha/js/ui-messages-mixin': {
                'StripeIntegration_Payments/js/mixins/messages-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    deps: [
        'Magento_Theme/js/theme'
    ]
};

require.config(config);
})();
(function() {
/*
* @Author: nguyen
* @Date:   2019-06-03 14:48:52
* @Last Modified by:   nguyen
* @Last Modified time: 2020-06-30 20:31:19
*/
var config = {
    config: {
        mixins: {
            'Magento_ReCaptchaFrontendUi/js/reCaptcha': {
                'js/mixins/reCaptcha-mixin': true
            }
        }
    },
};




require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            lensOptionData: 'Magento_Catalog/js/lensoption'
        }
    }
};
require.config(config);
})();



})(require);