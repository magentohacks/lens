if (!customElements.get("action-condition")) {
    class ActionCondition extends HTMLElement {
        constructor() {
            super();
            this.onMutation = this.onMutation.bind(this);
        }
        connectedCallback() {
            this.load();
            this.observer = new MutationObserver(this.onMutation);
            this.observer.observe(this, {
                childList: true,
                subtree: true
            });
        }
        disconnectedCallback() {
            this.observer.disconnect();
        }

        load() {
            var self = this,
                condition = this.querySelector('input[type="checkbox"]'),
                action = this.querySelector('.action');
            if (condition && action) {
                if(!condition.checked){
                    action.classList.add('disabled');
                }else{
                    action.classList.remove('disabled');
                }
                condition.addEventListener('change', function (event) {
                    if (event.currentTarget.checked) {
                        action.classList.remove('disabled');
                    } else {
                        action.classList.add('disabled');
                    }
                });
            }
        }
        onMutation(mutations) {
            this.load();
        }
    }

    customElements.define("action-condition", ActionCondition);
}

if (!customElements.get("accordion-tab")) {
    customElements.define("accordion-tab", class extends HTMLElement {
        constructor() {
            super();
        }
        connectedCallback() {
            this.load()
        }
        getDataset() {
            if(!this.Dataset) this.Dataset = this.datasetToObject(this.dataset);
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
        load() {
            let self = this;
            this.querySelectorAll('.tab-title').forEach( tab => {
                tab.addEventListener('click', function(event) {
                    let config = self.getDataset(),
                        tabPanel = tab.closest('.tab-panel'),
                        status = tabPanel.classList.toggle('active');
                    if(status && config.siblingsClose){
                        for (let panel of self.querySelectorAll('.tab-panel')) {
                            if (panel !== tabPanel) {
                                if(panel.classList.contains('active')){
                                    let tabTitle = panel.querySelector('.tab-title');
                                    if(tabTitle) tabTitle.click();
                                }
                            }
                        }
                    }
                    if(config.scrollIntoView) tabPanel.scrollIntoView({ block: "start", inline: "nearest", behavior: 'smooth' });
                });
            });
        }
    });
}

if (!customElements.get('back-to-top')) {
    customElements.define('back-to-top', class extends HTMLElement {
        constructor() {
            super();
        }
        connectedCallback() {
            this.load();
        }
        load() {
            var self = this,
                lastScrollTop = 0;
            window.addEventListener("scroll", (event) => {
                let status = window.scrollY,
                    documentHeight = Math.max(
                        document.body.scrollHeight,
                        document.documentElement.scrollHeight,
                        document.body.offsetHeight,
                        document.documentElement.offsetHeight,
                        document.body.clientHeight,
                        document.documentElement.clientHeight
                    );
                if (status + window.innerHeight >= documentHeight) {
                    document.documentElement.classList.add('scroll_down_end');
                } else {
                    document.documentElement.classList.remove('scroll_down_end');
                }
                if (status == 0) {
                    document.documentElement.classList.add('scroll_up_end');
                } else {
                    document.documentElement.classList.remove('scroll_up_end');
                }
                if (status > lastScrollTop) {
                    document.documentElement.classList.add('scroll_down');
                    document.documentElement.classList.remove('scroll_up', 'scroll_init');
                } else if (status == lastScrollTop) {
                    document.documentElement.classList.add('scroll_init');
                    document.documentElement.classList.remove('scroll_down', 'scroll_up');
                } else {
                    document.documentElement.classList.add('scroll_up');
                    document.documentElement.classList.remove('scroll_down', 'scroll_init');
                }
                lastScrollTop = status;
                if (status > 500) {
                    self.classList.add('show');
                } else {
                    self.classList.remove('show');
                }
                let percent = (status / (documentHeight - window.innerHeight)) * 100;
                self.style.setProperty("--height", `${percent.toFixed(2)}%`);
            });
            self.addEventListener("click", function (e) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return false;
            });
        }
    })
}

if (!customElements.get("campaign-bar")) {
    class CampaignBar extends HTMLElement {
        constructor() {
            super();
        }
        connectedCallback() {
            this.load()
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
    }
    customElements.define("campaign-bar", CampaignBar);
    customElements.define("top-bar", class extends CampaignBar { });
    customElements.define("footer-bar", class extends CampaignBar { });
}


if (!customElements.get('count-down')) {
    class CountDown extends HTMLElement {

        constructor() {
            super();
            this.settings = {
                layout: '<span class="box-count day"><span class="number">0</span><span class="text">Days</span></span><span class="box-count hrs"><span class="number">0</span><span class="text">Hrs</span></span><span class="box-count min"><span class="number">0</span><span class="text">Mins</span></span><span class="box-count secs"><span class="number">0</span> <span class="text">Secs</span></span>',
                leadingZero: true,
                countStepper: -1, // s: -1 // min: -60 // hour: -3600
                timeout: '<span class="timeout">Time out!</span>',
            }
            var self = this;
            document.addEventListener("CountDownUpdated", function (event) {
                self.init();
            });
            document.dispatchEvent(new CustomEvent('CountDownReady', {detail:self}));
        }

        connectedCallback() {
            let self = this;
            if (!localStorage.getItem("touchstart")) {
                document.addEventListener("touchstart", (event) => {
                    localStorage.setItem("touchstart", true);
                    self.init();
                }, {once : true});
                document.addEventListener("mouseover", (event) => {
                    localStorage.setItem("touchstart", true);
                    self.init();
                }, {once : true});
            }else{
                this.init();
            }
        }

        uniqid(length) {
            length = length || 10;
            var result = "",
                characters = "abcdefghijklmnopqrstuvwxyz0123456789",
                charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }

            return result;
        }

        init() {
            if (this.classList.contains("count-down-init")) return;
            var data  = this.getDataset();
            Object.assign(this.settings, data);
            this.classList.add('count-down-init');
            this.renderTimer();
        }

        getDataset() {
            if(!this.Dataset) this.Dataset = this.datasetToObject(this.dataset);
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
      
        renderTimer() {
            var data  = this.getDataset();
            if(!data.timer){
                var date = new Date(),
                    year = ('y' in data) ? Number(data.y.toString().replace("yyyy", date.getFullYear())) : date.getFullYear(),
                    mm   = ('m' in data) ? Number(data.m.toString().replace("mm", date.getMonth() + 1)) : date.getMonth() + 1,
                    dd   = ('d' in data) ? Number(data.d.toString().replace("dd", date.getDate() + 1)) : date.getDate() + 1,
                    hh   = ('h' in data) ? Number(data.h.toString().replace("hh", date.getHours())) : date.getHours(),
                    ii   = ('i' in data) ? Number(data.i.toString().replace("ii", date.getMinutes())) : date.getMinutes(),
                    ss   = ('s' in data) ? Number(data.s.toString().replace("ss", date.getSeconds())) : date.getSeconds(),
                    newDate = new Date(year, mm -1, dd, hh, ii, ss); // the month is 0-indexed

                    if('plusHour' in data) newDate.setHours(newDate.getHours() + Number(data.plusHour));
                    if('plusMin' in data) newDate.setMinutes(newDate.getMinutes() + Number(data.plusMin));
                    if('plusSec' in data) newDate.setSeconds(newDate.getSeconds() + Number(data.plusSec));

                data.timer = newDate;
            }
            var gsecs = data.timer;
            if (typeof gsecs === 'string') gsecs = gsecs.replace(/-/g, '/');
            if (isNaN(gsecs) || typeof gsecs === 'object') {
                var start = Date.parse(new Date());
                var end = isNaN(gsecs) ? Date.parse(gsecs) : gsecs;
                var end = (typeof gsecs === 'object') ? gsecs : Date.parse(gsecs);
                gsecs = (end - start) / 1000;
            }
            if (gsecs > 0) {
                var isLayout = this.querySelector('.min .number');
                if (!isLayout) {
                    this.innerHTML = this.settings.layout;                                   
                }
                this.CountBack(gsecs);
            } else {
                this.classList.add('the-end');
                if(this.settings.timeout) this.innerHTML = this.settings.timeout;
            }
        }

        calcage(secs, num1, num2) {
            var s = ((Math.floor(secs / num1) % num2)).toString();
            if (this.settings.leadingZero && s.length < 2) s = "0" + s;
            return "<b>" + s + "</b>";
        }

        CountBack(secs) {
            var self = this,
                countStepper = this.settings.countStepper,
                setTimeOutPeriod = (Math.abs(countStepper) - 1) * 1000 + 990;
            var count = setInterval(function timer() {
                if (secs < 0) {
                    clearInterval(count);
                    self.classList.add('the-end');
                    if(self.settings.timeout) self.innerHTML = self.settings.timeout;
                    return;
                }
                var day  = self.querySelector('.day .number'),
                    hour = self.querySelector('.hour .number, .hrs .number'),
                    min  = self.querySelector('.min .number'),
                    sec  = self.querySelector('.sec .number, .secs .number');
                if(day)  day.innerHTML  = self.calcage(secs, 86400, 100000);
                if(hour) hour.innerHTML = self.calcage(secs, 3600, 24);
                if(min)  min.innerHTML  = self.calcage(secs, 60, 60);
                if(sec)  sec.innerHTML  = self.calcage(secs, 1, 60);
                secs += countStepper;
                return timer;
            }(), setTimeOutPeriod);
        }

        appendStyle(css) {
            var style = document.createElement('style');
                style.setAttribute('type', 'text/css');
                style.textContent = css;
            document.head.appendChild(style);
        }

    }

    customElements.define("count-down", CountDown);
}


if (!customElements.get('count-up')) {
    class CountUp extends HTMLElement {

        constructor() {
            super();
            this.settings = {
                min: 0,
                max: 100,
                step: 1,
                speed: 1,
                infinite: true
            }
        }

        connectedCallback() {
             this.init();
        }

        init() {
            if (this.classList.contains("count-up-init")) return;
            var self = this,
                data  = this.getDataset();
                Object.assign(this.settings, data);
            this.classList.add('count-up-init');
            if ("IntersectionObserver" in window) {
                let counterObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            self.renderCounter();
                            self.classList.add('inView');
                            if(!data.infinite) counterObserver.unobserve(entry.target);
                        }else{
                            self.classList.remove('inView');
                        }
                    });
                });
                counterObserver.observe(self);                                  
            } else {
                self.renderCounter();
            }
        }

        getDataset() {
            if(!this.Dataset) this.Dataset = this.datasetToObject(this.dataset);
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

        renderCounter(counter){
            var self = this,
                min = this.settings.min,
                max = this.settings.max,
                step = this.settings.step,
                speed = this.settings.speed,
                counter = counter || min,
                element = this.querySelector('.counter');
            counter = counter + step;
            if (counter <= max) {
                element.innerHTML = counter.toString();
                setTimeout(function(){
                    self.renderCounter(counter);
                }, speed)    
            }else{
                element.innerHTML = max.toString();
            } 
        }

    }

    customElements.define("count-up", CountUp);
}

if (!customElements.get("tab-info")) {
    class SearchBar extends HTMLElement {
        constructor() {
            super();
        }
        connectedCallback() {
            this.load();
        }
        getSibling(element) {
            let siblings = [];
            for (let sibling of element.parentNode.children) {
                if (sibling !== element) siblings.push(sibling);
            }
            return siblings;
        }
        load() {
            var self = this,
                search = self.querySelector('input[name="q"]'),
                catalogsearch = self.querySelector(".catalogsearch-box");
            if(search){
                search.addEventListener("focus", (event) => {
                    document.documentElement.classList.add('open-search');
                });
                search.addEventListener("blur", (event) => {
                    let relatedTarget = event.relatedTarget;
                    if(relatedTarget && relatedTarget.closest('predictive-search')){
                        let searchSuggestions = relatedTarget.closest('predictive-search');
                        document.body.addEventListener('click', function(e) {
                            if(e.target.matches('predictive-search') || !e.target.closest('predictive-search')){
                                search.dispatchEvent(new Event('blur'), {bubbles: true});
                            }
                        });
                        return
                    }
                    setTimeout(function () { 
                        document.documentElement.classList.remove('open-search');
                    }, 100);
                });
                search.addEventListener('input', function (event) {
                    if (event.target.value){
                        document.documentElement.classList.add('input-search');
                    }else{
                        document.documentElement.classList.remove('input-search');
                    }
                    console.log('Input changed:', event.target.value);
                  });
                }
            if (catalogsearch) {
                var formSearch = self.querySelector("#search_mini_form"),
                    qsearch = self.querySelector("#qsearch"),
                    select = self.querySelector("#select-box-category"),
                    categories = self.querySelector("#categories-box");
                select.addEventListener('click', function (event) {
                    event.stopPropagation();
                    if (categories.style.display === 'none') {
                        categories.style.display = 'block';
                    } else {
                        categories.style.display = 'none';
                    }
                });
                categories.querySelectorAll("li").forEach(element => {
                    element.addEventListener("click", function () {
                        qsearch.value = element.getAttribute("data-q");
                        select.innerHTML = element.innerHTML;
                    });
                });

                formSearch.onsubmit = function () {
                    var search = self.querySelector("#search"),
                        qsearch = self.querySelector("#qsearch"),
                        csearch = qsearch.value ? '&cat=' + qsearch.value : '';
                    window.location = formSearch.action + '?q=' + search.value + csearch;
                    return false;
                };
            }
        }
    }

    customElements.define("search-bar", SearchBar);
}

if (!customElements.get("tab-info")) {
    class TabInfo extends HTMLElement {
        constructor() {
            super();
        }
        connectedCallback() {
            this.load();
        }
        getSibling(element) {
            let siblings = [];
            for (let sibling of element.parentNode.children) {
                if (sibling !== element) siblings.push(sibling);
            }
            return siblings;
        }
        load() {
            let self = this,
            tabNav = this.querySelector('.tab-nav');
            if(tabNav){
                tabNav.querySelectorAll('.tab-title').forEach(tab => {
                    tab.addEventListener('click', function(event) {
                        event.preventDefault();
                        let tabId = this.getAttribute('href'),
                            tabActive = this.closest('.tab'),
                            contentActive = self.querySelector(tabId);
                        tabActive.classList.add('active');
                        self.getSibling(tabActive).forEach(element => {
                            element.classList.remove("active");
                        });
                        contentActive.classList.add('active');
                        self.getSibling(contentActive).forEach(element => {
                            element.classList.remove("active");
                        });
                    })
                })
            }
        }
    }

    customElements.define("tab-info", TabInfo);
    customElements.define("product-information", class extends TabInfo { });
}


if (!customElements.get("trigger-click")) {
    class TriggerEvent extends HTMLElement {
        constructor() {
            super();
            this.namespace = this.tagName.toLowerCase().replace('trigger-', '');
        }
        connectedCallback() {
            this.load()
        }

        load() {
            let self = this;
            this.addEventListener(this.namespace, function(event){
                document.querySelectorAll(self.dataset.target).forEach(element => {
                    /* cancelable support event.preventDefault */
                    element.dispatchEvent(new Event(self.namespace, {bubbles: true, cancelable: true}));
                })
            });
        }
    }
    customElements.define("trigger-click", TriggerEvent);
    customElements.define("trigger-something", class extends TriggerEvent { });
}


if (!customElements.get("trigger-toggle")) {
    class TriggerToggle extends HTMLElement {
        constructor() {
            super();
            this.namespace = this.tagName.toLowerCase().replace('trigger-', '');
        }
        connectedCallback() {
            this.load()
        }

        datasetToObject(dataset, evalX) {
            let object = Object.assign({}, dataset);
            for (let property in object) {
                let value = object[property];
                try {
                    value = JSON.parse(value)
                } catch (e) {

                }
                if (evalX) {
                    try {
                        /* return value if is function */
                        value = (0, eval)('(' + value + ')');
                    } catch (e) {
                        value = value;
                    }
                }
                object[property] = value;
            }
            return object;
        }

        load() {
            let self = this;
            this.addEventListener('click', function(event){
                let config = self.datasetToObject(self.dataset, true),
                    target = document.querySelectorAll(config.target),
                    attributes = config.attributes || {},
                    toggle = !('toggle' in config && !config.toggle),
                    outside = !('outside' in config && !config.outside),
                    result;
                if(!target.length) target = [self];
                target.forEach(element => {
                    Object.entries(attributes).forEach(entry => {
                      const [key, value] = entry;
                      if(key == 'class'){
                        if(toggle){
                            result = element.classList.toggle(value)
                        }else{
                            element.classList.add(value)
                        }
                      }else{
                        if(toggle){
                            result = element.toggleAttribute(key);
                        }else{
                            element.setAttribute(key, value);
                        }
                      }
                    });
                })
                if(outside && result){
                    document.addEventListener('click', function(event) {
                        let contains = Array.from(target).some(function (el) {
                            return el.contains(event.target);
                        });
                        if (!(contains || self.contains(event.target))) {
                            self.dispatchEvent(new Event('click', {bubbles: true, cancelable: true}));
                        }
                    }, {capture : true, once : true});
                }
            });
        }
    }
    customElements.define("trigger-toggle", TriggerToggle);
}