if (!customElements.get("recently-viewed")) {
    customElements.define("recently-viewed", class extends HTMLElement {
        constructor() {
            super();
        }
        connectedCallback() {
            this.load();
            if (document.currentScript && document.currentScript.src) {
                this.getAppVersion(document.currentScript.src.split('/js/')[0].split('/').pop(), 'https://magepow.com/magento-2-recently-viewed-products.html');
            }
        }

        getDataset() {
            if (!this.Dataset) this.Dataset = this.datasetToObject(this.dataset);
            return this.Dataset;
        }

        datasetToObject(dataset) {
            return JSON.parse(JSON.stringify(dataset), (key, value) => {
                try {
                    return JSON.parse(value);
                } catch (e) {
                    return value;
                }
            });
        }

        getScreen() {
            let screen = [];
            screen['1'] = 'space-between';
            screen['361'] = 'mobile';
            screen['481'] = 'portrait';
            screen['576'] = 'landscape';
            screen['768'] = 'tablet';
            screen['992'] = 'notebook';
            screen['1200'] = 'laptop';
            screen['1480'] = 'desktop';
            screen['1920'] = 'widescreen';
            screen['1921'] = 'visible';

            return screen;
        }

        load() {
            let self = this,
                products = this.querySelector('grid-slider'),
                config = this.datasetToObject(self.dataset),
                storage = localStorage.getItem('recently_viewed_product'),
                items = storage ? JSON.parse(storage) : [],
                limit = config.limit || 10,
                currentProductId = '',
                exist = false,
                num = 0,
                productIds = [];
            Object.entries(items).reverse().forEach(function (entry) {
                if (num == limit) return false;
                let item = entry[1],
                    productId = item.product_id;
                if (config.scope_id != item.scope_id || productId == currentProductId) {
                    exist = true;
                    return;
                };
                productIds.push(productId);
                num++;
            });

            if (productIds.length) {
                let params = { 
                        product_ids: productIds.join(','),
                        scope_id: config.scope_id,
                        ajax: true
                    },
                    items = self.dataset.ajax ? JSON.parse(self.dataset.ajax) : {};
                Object.assign(params, items );
                let queryString = Object.keys(params).map(key => {
                    return encodeURIComponent(key) + '=' + encodeURIComponent(params[key])
                }).join('&');
                fetch(`${config.url}?${queryString}`).then((response) => response.text())
                    .then((responseText) => {
                        var html = new DOMParser().parseFromString(responseText, "text/html"),
                            source = html.querySelector('.product-items');
                        if (source) {
                            self.classList.add('active');
                            source.classList.add('grid-slider');
                            products.innerHTML = source.outerHTML;
                            document.body.dispatchEvent(new CustomEvent('contentUpdated', { bubbles: true, cancelable: true, detail: items }));
                            document.dispatchEvent(new Event('GridSliderUpdated'));
                        }
                    });
            }
        }
        getAppVersion(appName, href) {
            // this expression is to get the version string
            let regx = new RegExp('.*\/(.*?)\/assets\/', 'i');
            let result = regx.exec(href);
            let version = result ? result[1].replace(/\D/g, '') : '1.0.0';
            console.log(`%c ${appName} %c v${version}  %c`, "background: #555555; padding: 1px; margin-bottom: 2px; border-radius: 3px 0 0 3px; color: #fff", "background: #44cc11; padding: 1px; margin-bottom: 2px; border-radius: 0 3px 3px 0; color: #fff", "background:transparent", `🚀 ${href}`);

            return version;
        }
    });
}