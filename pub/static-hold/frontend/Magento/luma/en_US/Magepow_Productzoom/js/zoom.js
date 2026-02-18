class MageZoom {
    constructor() {
        this.minHeight = 200;
        this.minWindow = 350;
        this.maxWindow = 900;
        this.tryTime = 200;
        this.preload = true;
        this.touchDevices = false;
        this.zoomIgnore = 'zoom-ignore';
        this.zoomInit = 'zoom-init';
        this.imgSource = ['data-original', 'data-original-src', 'srcset', 'data-srcset', 'data-src', 'src'];
        this.options = {
            debugger: false,
            /* shopify-section-template not exist in old theme so incompatible old theme */
            section: 'shopify-section',
            sectionSelector: '[id^="shopify-section"]:not(".header-section, .section-header, .footer-section, .section-footer, #shopify-section-header, #shopify-section-footer, #shopify-section-footer-promotions, #shopify-section-newsletter-popup")',
            sectionExclude: 'header, product-modal, quick-order-list, .fancybox__container, .mfp-container, .pswp__container, .popup, .description, .product__description, .thumbnail-slider, product-recommendations, [id*="recommendations"], [class*="recommendations"], [id*="related"], [class*="related"], [id*="recently"], footer, product-tab, m-product-details-tabs, .t4s-product-tabs-wrapper, .tab-content',
            container: '[id^="shopify-section-template"][id*="main"], media-gallery, modal-opener, product-gallery, gallery-product, product-media, product-images',
            pages: 'product',
            Init: '',
            ZoomInit: '',
            ZoomIn: '',
            ZoomOut: '',
            zoomFactor: 1,
            ZoomIgnore: '.zoom-ignore',
            effectDuration: 600,
            sourceAttribute: 'data-zoom',
            originalImage: true,
            zoomType: 'window',
            marginWindow: 10
        };
        this.controller = new AbortController();
        this.signal = this.controller.signal;
        var self = this;
        document.addEventListener("domReady", function () {
            if (window.matchMedia("(pointer: coarse)").matches) {
                self.touchDevices = true;
                document.documentElement.classList.add('touchscreen');
            }
            var options = self.options,
                settings = document.querySelector('#magepowapps-zoom-settings');
            self.debugger = options.debugger;
            if (settings) {
                if (document.currentScript && document.currentScript.src) {
                    self.getAppVersion(document.currentScript.src.split('/js/')[0].split('/').pop(), 'https://magepow.com/magento-2-product-zoom.html');
                }
                self.settings = JSON.parse(settings.innerHTML);
                if (typeof self.settings === "object") {
                    var requires = ['container'];
                    requires.forEach(function (val) {
                        if (self.settings[val] == '') delete self.settings[val];
                    });
                    Object.assign(self.options, self.settings);
                }
            } else {
                self.logMsg(' ProductZoom Disabled!');
                return;
            }
            /* Disable zoom in mobile */
            if (self.options.zoomTouch == 'off' && self.touchDevices) return;

            const zoomType = new URLSearchParams(window.location.search).get('zoomType');
            if (zoomType) {
                Object.assign(self.options, { zoomType: zoomType });
            }
            if (document.body.classList.contains('rtl') || document.documentElement.classList.contains('rtl') || document.body.dir == "rtl") {
                self.RTL = true;
            }
            if (self.debugger) {
                self.logger(self.options);
            }

            if (options.hasOwnProperty('Init') && typeof options.Init === 'function') {
                options.Init();
            }

            document.body.addEventListener("MageZoom:ZoomInit", function (event) {
                var img = event.detail;
                if (options.hasOwnProperty('ZoomInit') && typeof options.Init === 'function') {
                    options.ZoomIn();
                }
            });

            document.body.addEventListener("MageZoom:ZoomIn", function (event) {
                var img = event.detail;
                if (options.hasOwnProperty('ZoomIn') && typeof options.Init === 'function') {
                    options.ZoomIn();
                }
            });

            document.body.addEventListener("MageZoom:ZoomOut", function (event) {
                var img = event.detail;
                if (options.hasOwnProperty('ZoomOut') && typeof options.Init === 'function') {
                    options.ZoomOut();
                }
            });

            self.initZoom();
            const debscanImage = self.debounce(() => {
                var images = self.scanImage();
                images.forEach((img) => {
                    self.zoomImage(img);
                });
            }, 0);
            var observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    var element = mutation.target;
                    if (!(element instanceof Element)) {
                        element = element.parentElement;
                    }
                    if (element.drift && mutation.type == 'attributes' && self.imgSource.includes(mutation.attributeName)) {
                        if (!element.complete) {
                            element.onload = function () {
                                var imgx = element.getAttribute(mutation.attributeName),
                                    currentSrc = element.currentSrc.replace(/^https?:/, '').trim();
                                if (imgx.includes(currentSrc)) {
                                    var imageUrl = self.getImageUrl(imgx);
                                    element.drift.setZoomImageURL(imageUrl);
                                    element.setAttribute(self.options.sourceAttribute, imageUrl);
                                }
                            }
                        } else {
                            var imgx = element.getAttribute(mutation.attributeName),
                                currentSrc = element.currentSrc.replace(/^https?:/, '').trim();
                            if (imgx.includes(currentSrc)) {
                                var imageUrl = self.getImageUrl(imgx);
                                element.drift.setZoomImageURL(imageUrl);
                                element.setAttribute(self.options.sourceAttribute, imageUrl);
                            }
                        }
                    } else if (element.closest('.drift-pane') || element.closest(self.options.sectionExclude) || element.closest(self.options.ZoomIgnore)) {
                        return;
                    }
                    // debscanImage();
                    var images = self.scanImage();
                    // var images = self.getImages(element);
                    images.forEach((img) => {
                        self.zoomImage(img);
                    });
                })
            });
            observer.observe(document.querySelector('main') || document.body, { attributes: true, childList: true, subtree: true });
        });
        if (/complete|interactive|loaded/.test(document.readyState)) {
            document.dispatchEvent(new Event('domReady'));
        } else {
            document.addEventListener('DOMContentLoaded', function () {
                document.dispatchEvent(new Event('domReady'));
            }, false);
        }
    }

    logMsg(msg) {
        console.log(`%c ${msg} `, "background: #44cc11; padding: 1px; margin-bottom: 2px; border-radius: 6px; color: #fff");
    }

    getAppVersion(appName, href) {
        // this expression is to get the version string
        let regx = new RegExp('.*\/(.*?)\/assets\/', 'i');
        let result = regx.exec(href);
        let version = result ? result[1].replace(/\D/g, '') : '1.0.0';
        console.log(`%c ${appName} %c v${version}  %c`, "background: #555555; padding: 1px; margin-bottom: 2px; border-radius: 3px 0 0 3px; color: #fff", "background: #44cc11; padding: 1px; margin-bottom: 2px; border-radius: 0 3px 3px 0; color: #fff", "background:transparent", `🚀 ${href}`);

        return version;
    }

    is(selector, element) {
        if (element instanceof NodeList || Array.isArray(element)) {
            return Array.from(element).some(function (item) {
                return item.matches(selector);
            });
        }
        return this.isElement(element) ? element.matches(selector) : false;
    }

    isElement(element) {
        // return element instanceof Element || element instanceof HTMLDocument;  
        return element instanceof Element;
    }

    scanImage() {
        var self = this,
            elements,
            images = [],
            selector = self.options.container;
        if (selector) {
            elements = document.querySelectorAll(selector);
            images = self.getImages(elements);
        }
        if (!images.length) {
            elements = self.detectProductGallery();
            images = self.getImages(elements);
            if (!images.length) {
                images = self.getImages(elements.previousElementSibling);
                if (!images.length) {
                    images = self.getImages(elements.nextElementSibling);
                }
            }
        }
        return images;
    }

    initZoom() {
        var self = this,
            elements,
            images = [];
        document.querySelector('html').classList.add('magezoom-init', 'magezoom-' + this.options.zoomType);
        images = self.scanImage();
        if (!images.length) {
            console.warn('Images does not exist!');
            return;
        }
        if (this.debugger) {
            console.log(elements);
        }
        images.forEach((img) => {
            self.zoomImage(img);
        });
        document.body.addEventListener('ZoomCreate', function (event) {
            var img = event.detail;
            if (img) self.zoomImage(img);
        });
        self.lightZoom();
    }
    lightZoom() {
        var self = this;
        document.querySelectorAll('modal-opener').forEach(element => {
            element.addEventListener('click', function (event) {
                if (event.target.matches('img') || event.target.closest('.motion-reduce, .product__media-icon')) {
                    var lightbox = element.querySelector('[aria-haspopup="dialog"]');
                    if (lightbox) lightbox.click();
                }
            });
        });
        document.body.addEventListener('ZoomImgClick', function (event) {
            var img = event.detail;
            if (img) {
                var next = img.nextElementSibling;
                if (next && self.is('button', next)) {
                    next.click();
                } else {
                    var lightbox = img.parentElement.parentElement.parentElement.querySelector('.js-photoswipe__zoom, a[rel="lightbox"], modal-trigger, button[x-ref="galleryFullscreenBtn"]');
                    if (lightbox) lightbox.click();
                }
            }
        });
    }
    tryAgain(time) {
        var self = this,
            time = time || 100;
        if (this.tryTime < 0) return;
        this.tryTime--;
        setTimeout(function () {
            {
                self.initZoom();
            }
        }, time);
    }
    zoomImage(img) {
        if (img.drift ||
            img.classList.contains('zoom-ignore') ||
            img.classList.contains("pswp__img") ||
            img.classList.contains("fancybox-image") ||
            img.classList.contains("fancybox-img") ||
            img.classList.contains("mfp-img") ||
            img.classList.contains("fslightbox-source") ||
            img.classList.contains("js-zoom-item") ||
            img.closest(".drift-zoom-pane")
        ) return;
        if (img.classList.contains('lazyload') || !this.isVisible(img) || !this.validateImage(img)) {
            this.watchImg(img);
            return;
        }
        var self = this,
            dataSource = self.options.sourceAttribute;
        if (!img.getAttribute(dataSource)) {
            img = self.prepareDataZoom(img);
            if (!img || !img.getAttribute(dataSource)) return;
        }
        img.classList.add(self.zoomInit);
        var pointerEvents = window.getComputedStyle(img, null).getPropertyValue('pointer-events');
        if (pointerEvents == 'none') {
            img.style.setProperty('pointer-events', 'auto', 'important');
        }
        if (self.preload) {
            self.preload = false;
            new Image().src = img.getAttribute(self.options.sourceAttribute);
        }
        img.addEventListener('click', function (event) {
            if (self.debugger) {
                self.logger('ZoomImgClick');
            }
            document.body.dispatchEvent(new CustomEvent('ZoomImgClick', { detail: event.target }));
        });
        var options = self.options;
        if (self.touchDevices) {
            if (!options.zoomTouch) options.zoomTouch = options.zoomType;
            options = (options.zoomTouch == 'lens') ? self.getZoomLens(img) : self.getZoomInner(img)
        } else {
            switch (options.zoomType) {
                case 'lens':
                    options = self.getZoomLens(img);
                    break;
                case 'inner':
                    options = self.getZoomInner(img);
                    break;
                default:
                    options = self.getZoomWindow(img);
            }
        }
        var drift = new Drift(img, options);
        img.drift = drift;
    }

    getPaneContainer() {
        if (!self.paneContainer) {
            var div = document.createElement('div');
            div.setAttribute('class', 'drift-pane');
            var appendZoom = document.querySelector('product-gallery-disable');
            if (appendZoom) {
                appendZoom.appendChild(div);
            } else {
                document.body.appendChild(div);
            }
            self.paneContainer = document.querySelector('.drift-pane');
        }
        return self.paneContainer;
    }

    getZoomLens(img) {
        var self = this,
            options = self.options;
        return Object.assign(options, {
            namespace: 'magezoom-lens',
            inlinePane: true,
            hoverBoundingBox: false
        });
    }

    getZoomInner(img) {
        var self = this,
            options = self.options,
            pane = self.getPaneContainer(),
            effect = self.options.zoomEffect,
            timing = { duration: parseInt(self.options.effectDuration), iterations: 0.5 };
        effect = effect ? self.zoomEffect() : self.zoomEffect('flyOutWindow');
        return Object.assign(options, {
            namespace: 'magezoom-inner',
            inlinePane: (self.touchDevices && self.zoomTouch == 'lens') ? true : 1,
            hoverBoundingBox: false,
            paneContainer: pane,
            onShow: function () {
                if (img.clientHeight < self.minHeight || img.clientWidth < self.minHeight) {
                    pane.style.display = 'none';
                    return;
                }
                self.setPositionInner(img);
                window.addEventListener("scroll", function () {
                    self.setPositionInner(img);
                }, { signal: self.signal });
                pane.animate(effect, timing);
                pane.style.position = 'absolute';
                pane.style.display = 'block';
            }
        });
    }

    getZoomWindow(img) {
        var self = this,
            options = self.options,
            pane = self.getPaneContainer(),
            effect = self.options.zoomEffect,
            timing = { duration: parseInt(self.options.effectDuration), iterations: 0.5 };
        return Object.assign(options, {
            namespace: 'magezoom-window',
            inlinePane: false,
            containInline: true,
            paneContainer: pane,
            onShow: function () {
                if (img.clientHeight < self.minHeight || img.clientWidth < self.minHeight) {
                    pane.style.display = 'none';
                    return;
                }
                self.setPositionWindow(img);
                window.addEventListener("scroll", function () {
                    self.setPositionWindow(img);
                }, { signal: self.signal });
                effect = effect ? self.zoomEffect() : self.zoomEffect('flyOutImageToWindow');
                pane.animate(effect, timing);
                pane.style.position = 'absolute';
                // pane.style.zIndex=99999;
                pane.style.display = 'block';
            },
            onHide: function () {
                // self.controller.abort();
                pane.style.display = 'none';
            }
        }
        );
    }

    setPositionInner(img) {
        var self = this,
            options = self.options,
            pane = self.getPaneContainer();
        const scrollX = window.pageXOffset;
        const scrollY = window.pageYOffset;
        var triggerRect = img.getBoundingClientRect();
        let inlineLeft = triggerRect.left + scrollX;
        let inlineTop = triggerRect.top + scrollY;
        if (inlineLeft < triggerRect.left + scrollX) {
            inlineLeft = triggerRect.left + scrollX;
        }
        if (inlineTop < triggerRect.top + scrollY) {
            inlineTop = triggerRect.top + scrollY;
        }
        pane.style.top = `${inlineTop}px`;
        pane.style.left = `${inlineLeft}px`;
        pane.style.width = `${img.clientWidth}px`;
        pane.style.height = `${img.clientHeight}px`;
    }

    setPositionWindow(img) {
        var self = this,
            options = self.options,
            pane = self.getPaneContainer();
        const scrollX = window.pageXOffset;
        const scrollY = window.pageYOffset;
        var windowWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0),
            zoomWidth = options.zoomWidth ? options.zoomWidth : img.clientWidth,
            zoomHeight = options.zoomHeight ? options.zoomHeight : img.clientHeight,
            aspectRatio = zoomWidth / zoomHeight,
            triggerRect = img.getBoundingClientRect(),
            left = triggerRect.left,
            right = windowWidth - triggerRect.right;

        let inlineLeft = triggerRect.left + scrollX;
        let inlineTop = triggerRect.top + scrollY;
        if (inlineLeft < triggerRect.left + scrollX) {
            inlineLeft = triggerRect.left + scrollX;
        }
        if (inlineTop < triggerRect.top + scrollY) {
            inlineTop = triggerRect.top + scrollY;
        }

        self.RTL = (left > right);
        if (aspectRatio > 2 || aspectRatio < 0.5) {
            self.minWindow = 600
        }
        if (zoomWidth < self.minWindow) {
            zoomWidth = self.minWindow;
        }
        if (zoomHeight < self.minWindow) {
            zoomHeight = self.minWindow;
        }
        if (zoomWidth > self.maxWindow) {
            zoomWidth = self.maxWindow;
        }
        if (zoomHeight > self.maxWindow) {
            zoomHeight = self.maxWindow;
        }
        if (self.RTL) {
            pane.style.removeProperty('right');
            var negative = (left < zoomWidth + options.marginWindow),
                leftPanel = negative ? options.marginWindow : left - (zoomWidth + options.marginWindow);
            pane.style.left = `${leftPanel}px`;
            if (negative) zoomWidth = zoomWidth - (zoomWidth - left) - 2 * options.marginWindow;
        } else {
            pane.style.removeProperty('left');
            var negative = (right < zoomWidth + options.marginWindow),
                rightPanel = negative ? options.marginWindow : right - (zoomWidth + options.marginWindow);
            pane.style.right = `${rightPanel}px`;
            if (negative) zoomWidth = zoomWidth - (zoomWidth - right) - 2 * options.marginWindow;
        }
        pane.style.top = `${inlineTop}px`;
        pane.style.width = `${zoomWidth}px`;
        pane.style.height = `${zoomHeight}px`;
    }

    prepareDataZoom(img) {
        var self = this,
            src,
            isZoom,
            dataSource = self.options.sourceAttribute;
        if (img.drift || img.getAttribute(dataSource)) return img;
        self.imgSource.every((data) => {
            src = img.getAttribute(data);
            if (src && src.includes('//')) {
                return false;
            } else {
                return true;
            }
        });
        if (!src && img.currentSrc) {
            src = img.currentSrc;
        }
        if (!src) {
            console.warn('Not found src in tag img');
            // self.tryAgain();
            return;
        }
        src = self.getImageUrl(src);
        /* Only create zoom source when image match condition */
        isZoom = (img.classList.contains('zoom') || src.includes('/catalog/product/') || !src.includes(window.location.hostname));
        if (src && isZoom) {
            if (src.includes('{') && src.includes('}')) {
                self.watchImg(img);
                return;
            }
            img.setAttribute(dataSource, src);
        } else {
            img.classList.add(self.zoomIgnore);
        }
        return img;
    }

    watchImg(img) {
        var self = this;
        if (!img.matches('img') || img.follow) return;
        img.follow = true;
        img.classList.add('zoom-watch');
        const observer = new MutationObserver((changes) => {
            changes.forEach(change => {
                if (change.attributeName.includes('src')) {
                    // const newValue = change.target.getAttribute(change.attributeName);
                    if (img.clientHeight == 0) return;
                    if (img.clientHeight >= self.minHeight) {
                        self.prepareDataZoom(img);
                        document.body.dispatchEvent(new CustomEvent('ZoomCreate', { detail: img }));
                    } else {
                        /* ignore image small & keep hidden image */
                        img.classList.add(self.zoomIgnore);
                    }
                    observer.disconnect();
                }
            });
        });
        observer.observe(img, { attributeFilter: ['src'] });
    }

    detectProductGallery() {
        var self = this,
            form = document.querySelector('main') || document.body;
        [
            'form[action="/cart/add"] [name="form_type"]',
            'form[action="/cart/add"] [name="quantity"]',
            'form[action="/cart/add"] [name="id"]',
            'form[action="/cart/add"]',
            '[name="id"]',
            'button[name="addd"]',
            'button[type="submit"]'
        ].every((selector) => {
            form = document.querySelector(selector);
            if (self.debugger) {
                console.log(form);
            }
            if (form) {
                return false;
            } else {
                return true;
            }
        });
        var section = this.getSection(form);
        if (section) return section;
    }

    getSection(element) {
        var tagName = element.tagName.toLowerCase();
        if (element.classList.contains('shopify-section') || element.classList.contains('section') || tagName === "html" || tagName === "body" || tagName === "section") {
            if (element.querySelector('img')) {
                return element;
            } else if (tagName === "section") {
                element = this.getSection(element.parentElement);
            }
        } else {
            element = this.getSection(element.parentElement);
        }
        return element;
    }

    getImages(elements) {
        var self = this,
            images = [];
        if (!elements) return images;
        if (!(elements instanceof NodeList)) {
            elements = [elements];
        }
        elements.forEach((element) => {
            if (!(element instanceof Element)) return;
            if (element instanceof HTMLImageElement) {
                images.push(element);
            } else {
                var imgs = element.querySelectorAll('img:not(.' + self.zoomIgnore + ')');
                if (imgs) {
                    imgs.forEach((img) => {
                        if (!self.excludeImage(img)) {
                            images.push(img);
                        }
                    });
                }
            }
        });
        return images;
    }

    getImageUrl(src) {
        const breakpoint = / \d*\w/g;
        const tags = Array.from(src.matchAll(breakpoint));
        if (tags.length) {
            const splitted = src.split(breakpoint)
            var size = 0;
            tags.forEach((screen, index) => {
                var image = splitted[index].replace(/^\,/, '');
                screen = screen[0].replace(/\D/g, '');
                if (screen > size && image.includes('//')) {
                    src = image;
                }
            });
        }
        const isWebP = /\.webp$/i.test(src);
        if (this.options.originalImage && !isWebP){
            src = src.replace(/\?width=\d+\&/, '?').replace(/\?width=\d+/, '').replace(/\&width=\d+/, '');
            src = src.replace(/\?height=\d+\&/, '?').replace(/\?height=\d+/, '').replace(/\&height=\d+/, '');
            src = src.replace(/\/cache\/\b[a-fA-F0-9]{32}\b/g, '');
        }
        if (this.debugger) {
            this.logger(src);
        }
        return src;
    }

    excludeImage(img) {
        return (
            !!img.drift ||
            !this.isVisible(img) ||
            !!img.closest(this.options.ZoomIgnore) ||
            !!img.closest(this.options.sectionExclude)
        );
    }

    validateImage(img) {
        if (this.isVisible(img) && img.clientHeight < this.minHeight) {
            return false;
        }
        return true;
    }

    isVisible(element) {
        return element.offsetWidth > 0 || element.offsetHeight > 0 || element.getClientRects().length > 0;
    }

    getErrorObject() {
        try { throw Error('') } catch (err) { return err; }
    }
    /* Funtion logger use console.log and console.group */
    logger(msg, group = true) {
        console.logger = function (msg, group = true) {
            if (group) {
                if (typeof group == 'boolean') {
                    console.group('console.logger >>');
                } else {
                    console.group(group);
                }
            }
            switch (typeof msg) {
                case 'object':
                    for (const [key, value] of Object.entries(msg)) {
                        console.log(`${key} :`, value);
                    }
                    break;
                default:
                    console.log(msg);
            }
            if (group) console.groupEnd();
        }
        console.logger(msg, group);
        var inLogger = this.getErrorObject();
        console.log(inLogger.stack);
    }

    zoomEffect(type) {
        var self = this;
        if (self.effect) return self.effect;
        type = type || self.options.zoomEffect;
        switch (type) {
            case 'flyOutWindow':
                self.effect = [
                    { transform: 'translate3d(0%, 0, 0) scale(0)' },
                    { transform: 'translate3d(0, 0, 0) scale(1)' },
                    { transition: 'all .8s linear both' }
                ];
                break;
            case 'flySpinningWindow':
                self.effect = [
                    { transform: 'scale(0) rotateZ(0)' },
                    { transform: 'scale(1) rotateZ(-360deg)' },
                    { transition: 'all 0.5s linear both' }
                ];
                break;
            case 'flySpinningImageToWindow':
                self.effect = [
                    { transform: self.RTL ? 'translate3d(100%, 0, 0) scale(0) rotateZ(0)' : 'translate3d(-100%, 0, 0) scale(0) rotateZ(0)' },
                    { transform: 'translate3d(0, 0, 0) scale(1) rotateZ(360deg)' },
                    { transition: 'all 0.5s linear both' }
                ];
                break;
            default:
                /* Default flyOutImageToWindow */
                self.effect = [
                    { transform: self.RTL ? 'translate3d(100%, 0, 0) scale(0)' : 'translate3d(-100%, 0, 0) scale(0)' },
                    { transform: 'translate3d(0, 0, 0) scale(1)' },
                    { transition: 'all 0.8s linear both' }
                ];
        }
        return self.effect;
    }
    debounce(fn, wait) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), wait);
        };
    }
    throttle(fn, delay) {
        let lastCall = 0;
        return function (...args) {
            const now = new Date().getTime();
            if (now - lastCall < delay) {
                return;
            }
            lastCall = now;
            return fn(...args);
        };
    }
}
(function (__wpcc) {
    __wpcc.d = __wpcc.d || {};
    __wpcc.d.scope = {};
    __wpcc.d.createTemplateTagFirstArg = function (a) {
        return a.raw = a
    };
    __wpcc.d.createTemplateTagFirstArgWithRaw = function (a, b) {
        a.raw = b;
        return a
    };
    __wpcc.d.getGlobal = function (a) {
        a = ["object" == typeof globalThis && globalThis, a, "object" == typeof window && window, "object" == typeof self && self, "object" == typeof global && global];
        for (var b = 0; b < a.length; ++b) {
            var c = a[b];
            if (c && c.Math == Math) return c
        }
        throw Error("Cannot find global object");
    };
    __wpcc.d.global = __wpcc.d.getGlobal(this);
    var g;
    "undefined" === typeof g && (g = function () { });
    g.p = "";
}).call(this || window, (window.__wpcc = window.__wpcc || {}));

(function (__wpcc) {
    var r = function (a) {
        return h ? a instanceof HTMLElement : a && "object" === typeof a && null !== a && 1 === a.nodeType && "string" === typeof a.nodeName
    },
        t = function (a, b) {
            b.forEach(function (c) {
                a.classList.add(c)
            })
        },
        u = function (a, b) {
            b.forEach(function (c) {
                a.classList.remove(c)
            })
        },
        x = function () {
            throw Error("Missing parameter");
        },
        y = function (a) {
            this.isShowing = !1;
            var b = void 0 === a.namespace ? null : a.namespace,
                c = void 0 === a.zoomFactor ? x() : a.zoomFactor;
            a = void 0 === a.containerEl ? x() : a.containerEl;
            this.settings = {
                namespace: b,
                zoomFactor: c,
                containerEl: a
            };
            this.openClasses = this._buildClasses("open");
            this._buildElement()
        },
        z = function (a) {
            a = void 0 === a ? {} : a;
            this._show = this._show.bind(this);
            this._hide = this._hide.bind(this);
            this._handleEntry = this._handleEntry.bind(this);
            this._handleMovement = this._handleMovement.bind(this);
            var b = void 0 === a.el ? x() : a.el,
                c = void 0 === a.zoomPane ? x() : a.zoomPane,
                e = void 0 === a.sourceAttribute ? x() : a.sourceAttribute,
                f = void 0 === a.handleTouch ? x() : a.handleTouch,
                q = void 0 === a.onShow ? null : a.onShow,
                k = void 0 === a.onHide ? null : a.onHide,
                v = void 0 === a.hoverDelay ? 0 : a.hoverDelay,
                w = void 0 === a.touchDelay ? 0 : a.touchDelay,
                l = void 0 === a.hoverBoundingBox ? x() : a.hoverBoundingBox,
                m = void 0 === a.touchBoundingBox ? x() : a.touchBoundingBox,
                n = void 0 === a.namespace ? null : a.namespace,
                p = void 0 === a.zoomFactor ? x() : a.zoomFactor,
                B = void 0 === a.boundingBoxContainer ? x() : a.boundingBoxContainer;
            this.settings = {
                el: b,
                zoomPane: c,
                sourceAttribute: e,
                handleTouch: f,
                onShow: q,
                onHide: k,
                hoverDelay: v,
                touchDelay: w,
                hoverBoundingBox: l,
                touchBoundingBox: m,
                namespace: n,
                zoomFactor: p,
                boundingBoxContainer: B,
                passive: void 0 === a.passive ? !1 : a.passive
            };
            if (this.settings.hoverBoundingBox || this.settings.touchBoundingBox) this.boundingBox = new y({
                namespace: this.settings.namespace,
                zoomFactor: this.settings.zoomFactor,
                containerEl: this.settings.boundingBoxContainer
            });
            this.enabled = !0;
            this._bindEvents()
        },
        A = function (a) {
            a = void 0 === a ? {} : a;
            this.HAS_ANIMATION = !1;
            if ("undefined" !== typeof document) {
                var b = document.createElement("div").style;
                this.HAS_ANIMATION = "animation" in b || "webkitAnimation" in b
            }
            this._completeShow = this._completeShow.bind(this);
            this._completeHide = this._completeHide.bind(this);
            this._handleLoad = this._handleLoad.bind(this);
            this.isShowing = !1;
            b = void 0 === a.container ? null : a.container;
            var c = void 0 === a.zoomFactor ? x() : a.zoomFactor,
                e = void 0 === a.inline ? x() : a.inline,
                f = void 0 === a.namespace ? null : a.namespace,
                q = void 0 === a.showWhitespaceAtEdges ? x() : a.showWhitespaceAtEdges,
                k = void 0 === a.containInline ? x() : a.containInline;
            this.settings = {
                container: b,
                zoomFactor: c,
                inline: e,
                namespace: f,
                showWhitespaceAtEdges: q,
                containInline: k,
                inlineOffsetX: void 0 ===
                    a.inlineOffsetX ? 0 : a.inlineOffsetX,
                inlineOffsetY: void 0 === a.inlineOffsetY ? 0 : a.inlineOffsetY,
                inlineContainer: void 0 === a.inlineContainer ? document.body : a.inlineContainer
            };
            this.openClasses = this._buildClasses("open");
            this.openingClasses = this._buildClasses("opening");
            this.closingClasses = this._buildClasses("closing");
            this.inlineClasses = this._buildClasses("inline");
            this.loadingClasses = this._buildClasses("loading");
            this._buildElement()
        },
        C = function (a, b) {
            b = void 0 === b ? {} : b;
            this.VERSION = "1.5.0";
            this.triggerEl =
                a;
            this.destroy = this.destroy.bind(this);
            if (!r(this.triggerEl)) throw new TypeError("`new Drift` requires a DOM element as its first argument.");
            a = b.namespace || null;
            var c = b.showWhitespaceAtEdges || !1,
                e = b.containInline || !1,
                f = b.inlineOffsetX || 0,
                q = b.inlineOffsetY || 0,
                k = b.inlineContainer || document.body,
                v = b.sourceAttribute || "data-zoom",
                w = b.zoomFactor || 3,
                l = void 0 === b.paneContainer ? document.body : b.paneContainer,
                m = b.inlinePane || 375,
                n = "handleTouch" in b ? !!b.handleTouch : !0,
                p = b.onShow || null,
                B = b.onHide || null,
                D = "injectBaseStyles" in
                    b ? !!b.injectBaseStyles : !0,
                E = b.hoverDelay || 0,
                F = b.touchDelay || 0,
                G = b.hoverBoundingBox || !1,
                H = b.touchBoundingBox || !1,
                I = b.boundingBoxContainer || document.body;
            b = b.passive || !1;
            if (!0 !== m && !r(l)) throw new TypeError("`paneContainer` must be a DOM element when `inlinePane !== true`");
            if (!r(k)) throw new TypeError("`inlineContainer` must be a DOM element");
            this.settings = {
                namespace: a,
                showWhitespaceAtEdges: c,
                containInline: e,
                inlineOffsetX: f,
                inlineOffsetY: q,
                inlineContainer: k,
                sourceAttribute: v,
                zoomFactor: w,
                paneContainer: l,
                inlinePane: m,
                handleTouch: n,
                onShow: p,
                onHide: B,
                injectBaseStyles: D,
                hoverDelay: E,
                touchDelay: F,
                hoverBoundingBox: G,
                touchBoundingBox: H,
                boundingBoxContainer: I,
                passive: b
            };
            this.settings.injectBaseStyles && !document.querySelector(".drift-base-styles") && (b = document.createElement("style"), b.type = "text/css", b.classList.add("drift-base-styles"), b.appendChild(document.createTextNode(".drift-bounding-box,.drift-zoom-pane{position:absolute;pointer-events:none}@keyframes noop{0%{zoom:1}}@-webkit-keyframes noop{0%{zoom:1}}.drift-zoom-pane.drift-open{display:block}.drift-zoom-pane.drift-closing,.drift-zoom-pane.drift-opening{animation:noop 1ms;-webkit-animation:noop 1ms}.drift-zoom-pane{overflow:hidden;width:100%;height:100%;top:0;left:0}.drift-zoom-pane-loader{display:none}.drift-zoom-pane img{position:absolute;display:block;max-width:none;max-height:none}")),
                a = document.head, a.insertBefore(b, a.firstChild));
            this._buildZoomPane();
            this._buildTrigger()
        },
        h = "object" === typeof HTMLElement;
    y.prototype._buildClasses = function (a) {
        var b = ["drift-" + a],
            c = this.settings.namespace;
        c && b.push(c + "-" + a);
        return b
    };
    y.prototype._buildElement = function () {
        this.el = document.createElement("div");
        t(this.el, this._buildClasses("bounding-box"))
    };
    y.prototype.show = function (a, b) {
        this.isShowing = !0;
        this.settings.containerEl.appendChild(this.el);
        var c = this.el.style;
        c.width = Math.round(a / this.settings.zoomFactor) + "px";
        c.height = Math.round(b / this.settings.zoomFactor) + "px";
        t(this.el, this.openClasses)
    };
    y.prototype.hide = function () {
        this.isShowing && this.settings.containerEl.removeChild(this.el);
        this.isShowing = !1;
        u(this.el, this.openClasses)
    };
    y.prototype.setPosition = function (a, b, c) {
        var e = window.pageXOffset,
            f = window.pageYOffset;
        a = c.left + a * c.width - this.el.clientWidth / 2 + e;
        b = c.top + b * c.height - this.el.clientHeight / 2 + f;
        a < c.left + e ? a = c.left + e : a + this.el.clientWidth > c.left + c.width + e && (a = c.left + c.width - this.el.clientWidth + e);
        b < c.top + f ? b = c.top + f : b + this.el.clientHeight > c.top + c.height + f && (b = c.top + c.height - this.el.clientHeight + f);
        this.el.style.left = a + "px";
        this.el.style.top = b + "px"
    };
    z.prototype._preventDefault = function (a) {
        a.preventDefault()
    };
    z.prototype._preventDefaultAllowTouchScroll = function (a) {
        this.settings.touchDelay && this._isTouchEvent(a) && !this.isShowing || a.preventDefault()
    };
    z.prototype._isTouchEvent = function (a) {
        return !!a.touches
    };
    z.prototype._bindEvents = function () {
        this.settings.el.addEventListener("mouseenter", this._handleEntry);
        this.settings.el.addEventListener("mouseleave", this._hide);
        this.settings.el.addEventListener("mousemove", this._handleMovement);
        var a = {
            passive: this.settings.passive
        };
        this.settings.handleTouch ? (this.settings.el.addEventListener("touchstart", this._handleEntry, a), this.settings.el.addEventListener("touchend", this._hide), this.settings.el.addEventListener("touchmove", this._handleMovement, a)) : (this.settings.el.addEventListener("touchstart",
            this._preventDefault, a), this.settings.el.addEventListener("touchend", this._preventDefault), this.settings.el.addEventListener("touchmove", this._preventDefault, a))
    };
    z.prototype._unbindEvents = function () {
        this.settings.el.removeEventListener("mouseenter", this._handleEntry);
        this.settings.el.removeEventListener("mouseleave", this._hide);
        this.settings.el.removeEventListener("mousemove", this._handleMovement);
        this.settings.handleTouch ? (this.settings.el.removeEventListener("touchstart", this._handleEntry), this.settings.el.removeEventListener("touchend", this._hide), this.settings.el.removeEventListener("touchmove", this._handleMovement)) : (this.settings.el.removeEventListener("touchstart",
            this._preventDefault), this.settings.el.removeEventListener("touchend", this._preventDefault), this.settings.el.removeEventListener("touchmove", this._preventDefault))
    };
    z.prototype._handleEntry = function (a) {
        this._preventDefaultAllowTouchScroll(a);
        this._lastMovement = a;
        "mouseenter" == a.type && this.settings.hoverDelay ? this.entryTimeout = setTimeout(this._show, this.settings.hoverDelay) : this.settings.touchDelay ? this.entryTimeout = setTimeout(this._show, this.settings.touchDelay) : this._show()
    };
    z.prototype._show = function () {
        if (this.enabled) {
            var a = this.settings.onShow;
            a && "function" === typeof a && a();
            this.settings.zoomPane.show(this.settings.el.getAttribute(this.settings.sourceAttribute), this.settings.el.clientWidth, this.settings.el.clientHeight);
            this._lastMovement && ((a = this._lastMovement.touches) && this.settings.touchBoundingBox || !a && this.settings.hoverBoundingBox) && this.boundingBox.show(this.settings.zoomPane.el.clientWidth, this.settings.zoomPane.el.clientHeight);
            this._handleMovement()
        }
    };
    z.prototype._hide = function (a) {
        a && this._preventDefaultAllowTouchScroll(a);
        this._lastMovement = null;
        this.entryTimeout && clearTimeout(this.entryTimeout);
        this.boundingBox && this.boundingBox.hide();
        (a = this.settings.onHide) && "function" === typeof a && a();
        this.settings.zoomPane.hide()
    };
    z.prototype._handleMovement = function (a) {
        if (a) this._preventDefaultAllowTouchScroll(a), this._lastMovement = a;
        else if (this._lastMovement) a = this._lastMovement;
        else return;
        if (a.touches) {
            a = a.touches[0];
            var b = a.clientX;
            var c = a.clientY
        } else b = a.clientX, c = a.clientY;
        a = this.settings.el.getBoundingClientRect();
        b = (b - a.left) / this.settings.el.clientWidth;
        c = (c - a.top) / this.settings.el.clientHeight;
        this.boundingBox && this.boundingBox.setPosition(b, c, a);
        var self = this;
        const image = new Promise((resolve, reject) => {
            try {
                var imgx = new Image();
                imgx.onload = function () {
                    resolve(imgx);
                }
                imgx.onerror = function (error) {
                    reject(error);
                }
                imgx.src = self.settings.el.getAttribute(self.settings.sourceAttribute);
            } catch (error) {
                reject(error);
            }
        }).then((imgx) => {
            self.settings.zoomPane.setPosition(b, c, a)
        }).catch(function (error) {
            console.warn("Load image fail: ", error);
        });
    };
    __wpcc.d.global.Object.defineProperties(z.prototype, {
        isShowing: {
            configurable: !0,
            enumerable: !0,
            get: function () {
                return this.settings.zoomPane.isShowing
            }
        }
    });
    A.prototype._buildClasses = function (a) {
        var b = ["drift-" + a],
            c = this.settings.namespace;
        c && b.push(c + "-" + a);
        return b
    };
    A.prototype._buildElement = function () {
        this.el = document.createElement("div");
        t(this.el, this._buildClasses("zoom-pane"));
        var a = document.createElement("div");
        t(a, this._buildClasses("zoom-pane-loader"));
        this.el.appendChild(a);
        this.imgEl = document.createElement("img");
        this.el.appendChild(this.imgEl)
    };
    A.prototype._setImageURL = function (a) {
        this.imgEl.setAttribute("src", a)
    };
    A.prototype._setImageSize = function (a, b) {
        var self = this;
        const image = new Promise((resolve, reject) => {
            try {
                var imgx = new Image();
                imgx.onload = function () {
                    resolve(imgx);
                }
                imgx.onerror = function (error) {
                    reject(error);
                }
                imgx.src = self.imgEl.src;
            } catch (error) {
                reject(error);
            }
        }).then((imgx) => {
            if (self.settings.zoomFactor > 1) {
                var level = self.settings.zoomFactor;
                var width = level * a,
                    height = level * b;
                // var width = level*imgx.naturalWidth,
                //     height = level*imgx.naturalHeight;
            } else {
                var level = (imgx.naturalWidth > a * 1.5) ? 1 : 2,
                    width = level * imgx.naturalWidth,
                    height = level * imgx.naturalHeight;
            }
            self.imgEl.style.width = `${width}px`;
            self.imgEl.style.height = `${height}px`;
            self.imgEl.style.setProperty("max-width", `${width}px`, "important");
            self.imgEl.style.setProperty("max-height", `${height}px`, "important");
        }).catch(function (error) {
            console.warn("Load image fail: ", error);
        });
    };
    A.prototype.setPosition = function (a, b, c) {
        var e = this.imgEl.offsetWidth,
            f = this.imgEl.offsetHeight,
            q = this.el.offsetWidth,
            k = this.el.offsetHeight,
            v = q / 2 - e * a,
            w = k / 2 - f * b,
            l = q - e,
            m = k - f,
            n = 0 < l,
            p = 0 < m;
        f = n ? l / 2 : 0;
        e = p ? m / 2 : 0;
        l = n ? l / 2 : l;
        m = p ? m / 2 : m;
        this.el.parentElement === this.settings.inlineContainer && (p = window.pageXOffset, n = window.pageYOffset, a = c.left + a * c.width - q / 2 + this.settings.inlineOffsetX + p, b = c.top + b * c.height - k / 2 + this.settings.inlineOffsetY + n, this.settings.containInline && (a < c.left + p ? a = c.left + p : a + q > c.left + c.width + p &&
            (a = c.left + c.width - q + p), b < c.top + n ? b = c.top + n : b + k > c.top + c.height + n && (b = c.top + c.height - k + n)), this.el.style.left = a + "px", this.el.style.top = b + "px");
        this.settings.showWhitespaceAtEdges || (v > f ? v = f : v < l && (v = l), w > e ? w = e : w < m && (w = m));
        this.imgEl.style.transform = "translate(" + v + "px, " + w + "px)";
        this.imgEl.style.webkitTransform = "translate(" + v + "px, " + w + "px)"
    };
    A.prototype._removeListenersAndResetClasses = function () {
        this.el.removeEventListener("animationend", this._completeShow);
        this.el.removeEventListener("animationend", this._completeHide);
        this.el.removeEventListener("webkitAnimationEnd", this._completeShow);
        this.el.removeEventListener("webkitAnimationEnd", this._completeHide);
        u(this.el, this.openClasses);
        u(this.el, this.closingClasses)
    };
    A.prototype.show = function (a, b, c) {
        this._removeListenersAndResetClasses();
        this.isShowing = !0;
        t(this.el, this.openClasses);
        this.imgEl.getAttribute("src") != a && (t(this.el, this.loadingClasses), this.imgEl.addEventListener("load", this._handleLoad), this._setImageURL(a));
        this._setImageSize(b, c);
        this._isInline ? this._showInline() : this._showInContainer();
        this.HAS_ANIMATION && (this.el.addEventListener("animationend", this._completeShow), this.el.addEventListener("webkitAnimationEnd", this._completeShow), t(this.el, this.openingClasses))
    };
    A.prototype._showInline = function () {
        this.settings.inlineContainer.appendChild(this.el);
        t(this.el, this.inlineClasses)
    };
    A.prototype._showInContainer = function () {
        this.settings.container.appendChild(this.el)
    };
    A.prototype.hide = function () {
        this._removeListenersAndResetClasses();
        this.isShowing = !1;
        this.HAS_ANIMATION ? (this.el.addEventListener("animationend", this._completeHide), this.el.addEventListener("webkitAnimationEnd", this._completeHide), t(this.el, this.closingClasses)) : (u(this.el, this.openClasses), u(this.el, this.inlineClasses))
    };
    A.prototype._completeShow = function () {
        this.el.removeEventListener("animationend", this._completeShow);
        this.el.removeEventListener("webkitAnimationEnd", this._completeShow);
        u(this.el, this.openingClasses)
    };
    A.prototype._completeHide = function () {
        this.el.removeEventListener("animationend", this._completeHide);
        this.el.removeEventListener("webkitAnimationEnd", this._completeHide);
        u(this.el, this.openClasses);
        u(this.el, this.closingClasses);
        u(this.el, this.inlineClasses);
        this.el.style.left = "";
        this.el.style.top = "";
        this.el.parentElement === this.settings.container ? this.settings.container.removeChild(this.el) : this.el.parentElement === this.settings.inlineContainer && this.settings.inlineContainer.removeChild(this.el)
    };
    A.prototype._handleLoad = function () {
        this.imgEl.removeEventListener("load", this._handleLoad);
        u(this.el, this.loadingClasses)
    };
    __wpcc.d.global.Object.defineProperties(A.prototype, {
        _isInline: {
            configurable: !0,
            enumerable: !0,
            get: function () {
                var a = this.settings.inline;
                return !0 === a || "number" === typeof a && window.innerWidth <= a
            }
        }
    });
    C.prototype._buildZoomPane = function () {
        this.zoomPane = new A({
            container: this.settings.paneContainer,
            zoomFactor: this.settings.zoomFactor,
            showWhitespaceAtEdges: this.settings.showWhitespaceAtEdges,
            containInline: this.settings.containInline,
            inline: this.settings.inlinePane,
            namespace: this.settings.namespace,
            inlineOffsetX: this.settings.inlineOffsetX,
            inlineOffsetY: this.settings.inlineOffsetY,
            inlineContainer: this.settings.inlineContainer
        })
    };
    C.prototype._buildTrigger = function () {
        this.trigger = new z({
            el: this.triggerEl,
            zoomPane: this.zoomPane,
            handleTouch: this.settings.handleTouch,
            onShow: this.settings.onShow,
            onHide: this.settings.onHide,
            sourceAttribute: this.settings.sourceAttribute,
            hoverDelay: this.settings.hoverDelay,
            touchDelay: this.settings.touchDelay,
            hoverBoundingBox: this.settings.hoverBoundingBox,
            touchBoundingBox: this.settings.touchBoundingBox,
            namespace: this.settings.namespace,
            zoomFactor: this.settings.zoomFactor,
            boundingBoxContainer: this.settings.boundingBoxContainer,
            passive: this.settings.passive
        })
    };
    C.prototype.setZoomImageURL = function (a) {
        this.zoomPane._setImageURL(a)
    };
    C.prototype.disable = function () {
        this.trigger.enabled = !1
    };
    C.prototype.enable = function () {
        this.trigger.enabled = !0
    };
    C.prototype.destroy = function () {
        this.trigger._hide();
        this.trigger._unbindEvents()
    };
    __wpcc.d.global.Object.defineProperties(C.prototype, {
        isShowing: {
            configurable: !0,
            enumerable: !0,
            get: function () {
                return this.zoomPane.isShowing
            }
        },
        zoomFactor: {
            configurable: !0,
            enumerable: !0,
            get: function () {
                return this.settings.zoomFactor
            },
            set: function (a) {
                this.settings.zoomFactor = a;
                this.zoomPane.settings.zoomFactor = a;
                this.trigger.settings.zoomFactor = a;
                this.boundingBox.settings.zoomFactor = a
            }
        }
    });
    Object.defineProperty(C.prototype, "isShowing", {
        get: function () {
            return this.isShowing
        }
    });
    Object.defineProperty(C.prototype, "zoomFactor", {
        get: function () {
            return this.zoomFactor
        },
        set: function (a) {
            this.zoomFactor = a
        }
    });
    C.prototype.setZoomImageURL = C.prototype.setZoomImageURL;
    C.prototype.disable = C.prototype.disable;
    C.prototype.enable = C.prototype.enable;
    C.prototype.destroy = C.prototype.destroy;
    window.Drift = C;
}).call(this || window, (window.__wpcc = window.__wpcc || {}));

new MageZoom();