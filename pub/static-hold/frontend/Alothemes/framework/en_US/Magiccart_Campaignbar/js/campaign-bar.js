if (!customElements.get("campaign-bar")) {
    class CampaignBar extends HTMLElement {
        constructor() {
            super();
        }
        connectedCallback() {
            this.load();
            if (document.currentScript && document.currentScript.src) {
                this.getAppVersion(document.currentScript.src.split('/js/')[0].split('/').pop(), 'https://magepow.com/magento-campaign-bar.html');
            }
        }
        load() {
            let self = this,
            closeX = this.querySelector('.close-x');
            if(closeX){
                closeX.addEventListener('click', (event) => {
                    self.classList.add('close-bar');
                    self.classList.remove('show');
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
    }
    customElements.define("campaign-bar", CampaignBar);
    customElements.define("top-bar", class extends CampaignBar { });
    customElements.define("footer-bar", class extends CampaignBar { });
}
