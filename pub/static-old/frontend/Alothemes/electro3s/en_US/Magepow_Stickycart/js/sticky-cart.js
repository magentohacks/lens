class StickyCart extends HTMLElement {
    constructor() {
        super();
        this.options = {
            scrollHeight: 0,
            hiddenBottom: true,
            typeProduct: 'simple'
        };
    }
    connectedCallback() {
        this.init();
        if (document.currentScript && document.currentScript.src){
            this.getAppVersion(document.currentScript.src.split('/js/')[0].split('/').pop(), 'https://magepow.com/magento-sticky-add-to-cart.html');
        }
    }

    datasetToObject(dataset) {
        return JSON.parse(JSON.stringify(dataset), (key, value) => {
            if (value === "null") return null;
            if (value === "true") return true;
            if (value === "false") return false;
            if (!isNaN(value)) return Number(value);
            try {
                return JSON.parse(value);
            } catch (e) {
                return value;
            }
        });
    }

    getDataset() {
        if (!this.Dataset) this.Dataset = this.datasetToObject(this.dataset);
        return this.Dataset;
    }

    init() {
        let self = this,
            options = Object.assign(this.options, this.getDataset()),
            buttonAddToCart = document.querySelector('#product-addtocart-button'),
            buttonBundle = document.querySelector('#bundle-slide'),
            buttonSticky = self.querySelector('#btnSticky');
        if (buttonBundle) {
            var buttonAction = buttonBundle;
            buttonSticky.classList.add('customize');
        } else {
            var buttonAction = buttonAddToCart;
        }
        if (!buttonAction) return;
        var scrollHeight = options.scrollHeight ? options.scrollHeight : buttonAction.offsetTop;
        window.addEventListener("scroll", (event) => {
            let y = window.scrollY,
                documentHeight = Math.max(
                    document.body.scrollHeight,
                    document.documentElement.scrollHeight,
                    document.body.offsetHeight,
                    document.documentElement.offsetHeight,
                    document.body.clientHeight,
                    document.documentElement.clientHeight
                );
            var hiddenBottom = options.hiddenBottom ? (y + window.innerHeight == documentHeight) : '';
            if (y > scrollHeight && !hiddenBottom) {
                document.body.classList.add('show-add-cart-bottom');
                self.classList.add("sticky_show_atc");
            } else {
                document.body.classList.remove('show-add-cart-bottom');
                self.classList.remove("sticky_show_atc");
            }
        });
        var qtySticky = self.querySelector('#qtySticky'),
            groupedQty = document.querySelectorAll('#product_addtocart_form input[name^="super_group"]'),
            qtyMain = document.querySelector('#qty, input[name="qty"][form="product_addtocart_form"], #bundleSummary input[name="qty"]');
        document.querySelectorAll('#product_addtocart_form .increase, #product_addtocart_form .reduced').forEach((element) => {
            element.addEventListener("click", (event) => {
                let qtyCtl = element.closest('.qty .control');
                if (qtyCtl){
                    let qty = qtyCtl.querySelector('#qty');
                    if (qty) qtySticky.value =qty.value;
                }
            });
        });
        self.querySelectorAll('.increase, .reduced').forEach((element) => {
            element.addEventListener("click", (event) => {
                if (qtyMain) qtyMain.value = qtySticky.value;
                groupedQty.forEach((gqty) => {
                    gqty.value = qtySticky.value;
                });
            });
        });
        if (qtyMain) qtyMain.addEventListener("change", (event) => {
            qtySticky.value = qtyMain.value;
        });
        qtySticky.addEventListener("change", (event) => {
            if (qtyMain) qtyMain.value = qtySticky.value;
            groupedQty.forEach((gqty) => {
                gqty.value = qtySticky.value;
            });
        });

        buttonSticky.addEventListener('click', function (event) {
            var $this = this;
            $this.textContent = buttonAddToCart.querySelector('span') ? buttonAddToCart.querySelector('span').textContent : buttonAddToCart.textContent;
            $this.setAttribute("disabled", "disabled");
            setTimeout(function () {
                $this.removeAttribute("disabled");
                $this.textContent = buttonAddToCart.querySelector('span') ? buttonAddToCart.querySelector('span').textContent : buttonAddToCart.textContent;
            }, 1500);
            setTimeout(function () {
                $this.textContent = buttonAddToCart.querySelector('span') ? buttonAddToCart.querySelector('span').textContent : buttonAddToCart.textContent;
            }, 3000);
            if ($this.classList.contains('customize')) {
                buttonBundle.click();
                buttonSticky.classList.remove('customize');
            } else {
                buttonAddToCart.click();
            }
        });
        self.querySelectorAll('.quantity').forEach((spinner) => {
            var input = spinner.querySelector('input[type="number"]'),
                btnUp = spinner.querySelector('.quantity-up'),
                btnDown = spinner.querySelector('.quantity-down'),
                min = input.getAttribute('min'),
                max = input.getAttribute('max');
            if (btnUp) btnUp.addEventListener('click', function () {
                var oldValue = parseFloat(input.value);
                if (oldValue >= max) {
                    var newVal = oldValue;
                } else {
                    var newVal = oldValue + 1;
                }
                spinner.querySelector("input").value = newVal;
                spinner.querySelector("input").dispatchEvent(new Event('change'));
            });
            if (btnDown) btnDown.addEventListener('click', function () {
                var oldValue = parseFloat(input.value);
                if (oldValue <= min) {
                    var newVal = oldValue;
                } else {
                    var newVal = oldValue - 1;
                }
                spinner.querySelector("input").value = newVal;
                spinner.querySelector("input").dispatchEvent(new Event('change'));
            });
        });
    }

    getAppVersion(appName, href) {
        // this expression is to get the version string
        let regx = new RegExp('.*\/(.*?)\/assets\/', 'i');
        let result = regx.exec(href);
        let version = result ? result[1].replace(/\D/g, '') : '1.0.0';
        console.log(`%c ${appName} %c v${version}  %c`, "background: #555555; padding: 1px; margin-bottom: 2px; border-radius: 3px 0 0 3px; color: #fff", "background: #44cc11; padding: 1px; margin-bottom: 2px; border-radius: 0 3px 3px 0; color: #fff", "background:transparent", `🚀 ${href}`);

        return version;
    }

}

customElements.define("sticky-cart", StickyCart);
  