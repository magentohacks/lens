if (!customElements.get('magepow-infinitescroll')) {
	class MagepowInfinitescroll extends HTMLElement {
		constructor() {
			super();
		}
		connectedCallback() {
			this.container = this.closest('.infinitescroll-init');
			if (this.container) this.container.classList.add('infinitescroll-callback');
		}
		disconnectedCallback() {
			// document.body.dispatchEvent(new Event('collectionUpdated'));
			this.container.classList.remove('infinitescroll-callback');
			document.body.dispatchEvent(new Event('tryTime'));
			setTimeout(function () {
				document.body.dispatchEvent(new Event('collectionUpdated'));
			}, 50);
		}
	}
	customElements.define("magepow-infinitescroll", MagepowInfinitescroll);
}

try {
	class elementX {
		getBrowser() {
			var ua = navigator.userAgent,
				tem,
				M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
			if (/trident/i.test(M[1])) {
				tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
				return 'IE ' + (tem[1] || '');
			}
			if (M[1] === 'Chrome') {
				tem = ua.match(/\b(OPR|Edge)\/(\d+)/);
				if (tem != null) return tem.slice(1).join(' ').replace('OPR', 'Opera');
			}
			M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
			if ((tem = ua.match(/version\/(\d+)/i)) != null) M.splice(1, 1, tem[1]);
			return M.join(' ');
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
		isContains(selector, element) {
			if (element instanceof NodeList || Array.isArray(element)) {
				return Array.from(element).some(function (item) {
					return item.querySelector(selector);
				});
			} else if (element instanceof Element) {
				return element.querySelector(selector);
			}
		}
		not(selector, element) {
			var self = this;
			if ((element instanceof NodeList)) {
				element = Array.from(element);
			}
			if (Array.isArray(element)) {
				return element.filter((el, index, array) => {
					return !self.is(selector, el);
				});
			}
			if (!this.is(selector, element)) {
				return element;
			}
		}
		find(selector, element) {
			var self = this,
				elements = [];
			if ((element instanceof Element)) {
				element = [element];
			}
			element.forEach(el => {
				el.querySelectorAll(selector).forEach(item => {
					elements.push(item);
				})
			});
			return elements;
		}
		parents(selector, element) {
			if ((element instanceof NodeList)) {
				var parents = Array.from(element).map((item) => item.closest(selector))
					.filter((el, index, array) => {
						/* remove null value */
						return el ? array.indexOf(el) === index : false;
					});
				return parents;
			} else {
				return element.closest(selector);
			}
		}
		getFirstElement(elements) {
			return Array.from(elements).shift();
		}
		getLastElement(elements) {
			return Array.from(elements).pop();
		}
		isHidden(element) {
			var style = window.getComputedStyle(element);
			return (style.display === 'none')
		}
		isVisible(element) {
			return element.offsetWidth > 0 || element.offsetHeight > 0 || element.getClientRects().length > 0;
		}
		fadeIn(el, time) {
			var opacityX = el.style.opacity;
			var last = +new Date();
			(function fade() {
				var opacity = +el.style.opacity + (new Date() - last) / time;
				el.style.opacity = (opacity < 1) ? opacity : 1;
				last = +new Date();

				if (+el.style.opacity < 1) {
					(window.requestAnimationFrame && requestAnimationFrame(fade)) || setTimeout(fade, 16);
				} else {
					el.style.opacity = opacityX;
				}
			})();
		}
		capitalizeFirstLetter(string) {
			return string.charAt(0).toUpperCase() + string.slice(1);
		}
		isInViewport(element) {
			const rect = element.getBoundingClientRect();
			return (
				rect.top >= 0 &&
				// rect.left >= 0 &&
				rect.bottom <= ((window.innerHeight + rect.height) || document.documentElement.clientHeight) &&
				rect.right <= (window.innerWidth || document.documentElement.clientWidth)
			);
		}
		getInViewport(elements) {
			var self = this,
				inView = [];
			elements.forEach((item) => {
				if (self.isInViewport(item)) {
					item.classList.add('inView');
					inView.push(item);
				} else {
					item.classList.remove('inView');
				}
			});
			return inView;
		}
		datasetToObject(dataset) {
			var object = Object.assign({}, dataset);
			for (var property in object) {
				var value = object[property];
				switch (value) {
					case null:
						value = null;
						break;
					case false:
						value = false;
						break;
					case true:
						value = true;
						break;
					case !isNaN(value):
						value = Number(value);
						break;
					default:
						try {
							value = JSON.parse(value);
						} catch (e) {
							// value = value;
						}
						try {
							value = (0, eval)('(' + value + ')');
						} catch (e) {
							value = value;
						}
				}
				object[property] = value;
			}
			return object;
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
		}

		loggerDeveloper(msg, developer = true) {
			if (!developer || !this.developer) return;
			this.logger(msg, true);
		}

		logMsg(msg) {
			console.log(`%c ${msg} `, "background: #44cc11; padding: 1px; margin-bottom: 2px; border-radius: 6px; color: #fff");
		}
	}
	(function () {
		"use strict";
		class IAS extends elementX {
			constructor(element, settings) {
				super();
				this.controller = new AbortController();
				this.signal = this.controller.signal;
				this.INFINITE = -1;
				this.options = Object.assign({
					plan: 1,
					smart: true,
					shopify: false,
					autoLink: true,
					nextAppendTo: false,
					prevPrependTo: false,
					item: ':scope > *:not([id^="ias_spinner_"])',
					itemInitClass: 'initscroll',
					itemAjaxClass: 'ajaxscroll',
					itemHide: false,
					itemRemove: false,
					IntersectionObserver: true,
					container: '.listing',
					nextSelector: '.next',
					tryNext: false,
					pagination: false,
					pageHash: false,
					pageParam: 'p',
					resetPage: false,
					data: {},
					delay: 100,
					duration: 400,
					fadeIn: true,
					negativeMargin: 10,
					uid: (new Date()).getTime(),
					eventHandler: 'scroll',
					/* IASSpinner */
					src: '🚀',
					htmlSpinner: '<div style="text-align: center;"><img src="{src}"/></div>',
					/* IASNoneLeft */
					textNoneLeft: 'You reached the end.',
					htmlNoneLeft: '<div><div class="x-back-to-top"><div class="arrow"><span></span><span></span></div></div></div>',
					/* IASTrigger */
					textTrigger: 'Load more items',
					htmlTriggerNext: '<div><button class="load-more load-next">{text}</button></div>',
					textTriggerPrev: 'Load previous items',
					htmlTriggerPrev: '<div><button class="load-more load-prev">{text}</button></div>',
					offset: -1,
					backToTopOffset: -1,
					/* IASHistory */
					prevSelector: ".prev",
					/* listeners */
					ready: null,
					scroll: null,
					next: null,
					prev: null,
					load: null,
					loaded: null,
					render: null,
					rendered: null
				}, settings);
				this.prevResult = '';
				this.initItems = [];
				this.prevUrl = '';
				this.uid = this.options.uid;
				this.offsetTrigger = this.options.offset;
				/* Custom Effect Theme */
				this.aosAnimate = false;
				this.ccAnimate = false;
				this.revealOnScroll = false;
				this.animationItemRevealed = false;
				/* End Custom Effect Theme */
				this.$scrollContainer = window;
				if (this.options.data && this.options.data.hasOwnProperty('section_id')) {
					var sectionSelector = '#shopify-section-' + this.options.data['section_id'];
					var section = element.querySelector(sectionSelector);
					if (section) {
						element = section;
					}
				}
				this.$container = element;
				this.$itemsContainer = element.querySelector(this.options.container);
				this.initialize();
			}
			/**
			 * Initialize IAS
			 * Note: Should be called when the document is ready
			 */
			initialize() {
				this.ajaxPage = 0;
				this.currentPage = this.getQueryParam(window.location.href, this.options.pageParam);
				this.currentPage = this.currentPage ? parseInt(this.currentPage) : 1;
				this.prevPage = this.currentPage - 1;
				this.nextPage = this.currentPage + 1;
				this.isBound = false;
				this.scrollHandlerX = this.scrollHandler.bind(this);
				this.initItems = this.getItems(this.$container);
				this.nextUrl = this.getNextUrl();
				this.prevUrl = this.getPrevUrl();
				this.hidePagination();
				this.IASSpinner();
				this.IASNoneLeft();
				this.IASTrigger();
				this.IASHistory();
				if (typeof this.options.ready === 'function') {
					this.$container.addEventListener('ready', this.options.ready.bind(this), { signal: this.signal });
				}
				this.$container.dispatchEvent(new Event('ready'));
				// start loading next page if content is shorter than page fold
				if (this.nextUrl) {
					if (this.offsetTrigger == 0) {
						this.$container.dispatchEvent(new Event('showTriggerNext'));
					} else {
						this.bind();
						if (this.getCurrentScrollOffset(this.$scrollContainer) >= this.getScrollThreshold()) {
							if (this.buttonNext) this.buttonNext.click();
						}
					}
				}
				if (this.prevUrl) {
					if (this.getScrollTop() == 0) {
						/* Placeholder for event scroll to 0 */
						window.scrollTo(0, 0.5);
					}
					var currentScrollOffset = this.getCurrentScrollOffset(this.$scrollContainer),
						firstItemScrollThreshold = this.getScrollThresholdFirstItem();
					currentScrollOffset -= (this.$scrollContainer == window) ? this.$scrollContainer.innerHeight : this.$scrollContainer.clientHeight;
					if (currentScrollOffset <= firstItemScrollThreshold) {
						if (window.boostSDAppConfig || window.boostSDTaeUtils || window.boostSDData) {
							/* Disable Prev when use with https://apps.shopify.com/product-filter-search */
						} else {
							if (this.buttonPrev) this.buttonPrev.click();
						}
					} else {
						this.bind();
					}
				}
			}
			/**
			 * Binds IAS to DOM events
			 */
			bind() {
				if (this.isBound) return;
				this.$scrollContainer.addEventListener('scroll', this.scrollHandlerX, { signal: this.signal });
				this.isBound = true;
			}
			/**
			 * Unbinds IAS to events
			 */
			unbind() {
				if (!this.isBound) return;
				this.$scrollContainer.removeEventListener('scroll', this.scrollHandlerX, { signal: this.signal });
				// this.$container.querySelectorAll('[class^="ias"]').forEach((element) => {
				//     element.style.display = 'none';
				// });
				this.isBound = false;
			}
			/**
			 * Destroy IAS to events
			 */
			destroy() {
				this.controller.abort();
				this.$container.querySelectorAll('[id^="ias"][id$="' + this.uid + '"]').forEach((item) => {
					item.remove();
				});
			}
			/**
			 * Refresh IAS to events
			 */
			refresh() {
				this.controller.abort();
				this.initialize();
			}
			/**
			 * Scroll event handler
			 * Note: calls to this functions should be throttled
			 */
			scrollHandler() {
				var self = this,
					currentScrollOffset = this.getCurrentScrollOffset(this.$scrollContainer),
					scrollThreshold = this.getScrollThreshold(),
					lastElement = this.getLastItem();
				// the throttle method can call the scrollHandler even thought we have called unbind()
				if (!this.isBound) return;
				// invalid scrollThreshold. The DOM might not have loaded yet...
				if (this.INFINITE == scrollThreshold) {
					return;
				}
				this.$container.dispatchEvent(new CustomEvent('scroll', { detail: { 'currentScrollOffset': currentScrollOffset, 'scrollThreshold': scrollThreshold } }));
				if (self.options.IntersectionObserver && 'IntersectionObserver' in window) {
					if (!lastElement.ionext) {
						const observerNext = new IntersectionObserver((entries, observer) => {
							entries.forEach(function (entry) {
								if (entry.isIntersecting) {
									if (self.buttonNext) self.buttonNext.click();
									observerNext.unobserve(entry.target);
									lastElement.ionext = false;
								}
							});
						}, { threshold: 0.5 });
						observerNext.observe(lastElement);
					}
					lastElement.ionext = true;
				} else if (this.isInViewport(lastElement)) {
					if (this.buttonNext) this.buttonNext.click();
				}
			}

			privateClick(element) {
				/* this block trigger click from 3 party */
				element.dispatchEvent(new CustomEvent('click', { detail: { private: true } }));
			}

			intersectionHandler(element) {
				var self = this;
				if ("IntersectionObserver" in window) {
					let infiniteObserver = new IntersectionObserver(function (entries, observer) {
						entries.forEach(function (entry) {
							if (entry.isIntersecting) {
								let el = entry.target;
								el.click();
								// infiniteObserver.unobserve(entry.target);
							}
						});
					});
					infiniteObserver(element)
				}
			}

			getScrollTop() {
				// document.scrollingElement.scrollTop;
				// document.documentElement.scrollTop;
				// document.body.scrollTop
				return Math.max(document.body.scrollTop, document.documentElement.scrollTop);
			}
			/**
			 * Returns the last item currently in the DOM
			 * @returns {object}
			 */
			getLastItem() {
				var self = this;
				var items = self.$itemsContainer.querySelectorAll(self.options.item);
				var lastElement = Array.from(items)
					.filter(item => !item.matches(self.options.pagination) && !item.matches('[id^="ias_spinner_"]') && !self.isHidden(item))
					.pop();
				if (!lastElement && this.options.smart) {
					var lastElement = Array.from(self.$itemsContainer.children)
						.filter(item => !item.matches(self.options.pagination) && !item.matches('[id^="ias_spinner_"]') && !self.isHidden(item))
						.pop();
				}
				return lastElement;
			}

			/**
			 * Returns the first item currently in the DOM
			 * @returns {object}
			 */
			getFirstItem() {
				var self = this;
				var items = self.$itemsContainer.querySelectorAll(self.options.item);
				var firstElement = Array.from(items)
					.filter(item => !item.matches(self.options.pagination) && !item.matches('[id^="ias_spinner_"]') && !self.isHidden(item))
					.shift();
				if (!firstElement && this.options.smart) {
					var firstElement = Array.from(self.$itemsContainer.children)
						.filter(item => !item.matches(self.options.pagination) && !item.matches('[id^="ias_spinner_"]') && !self.isHidden(item))
						.shift();
				}
				return firstElement;
			}
			/**
			 * Returns scroll threshold. This threshold marks the line from where
			 * IAS should start loading the next page.
			 * @param negativeMargin defaults to {this.negativeMargin}
			 * @return {number}
			 */
			getScrollThreshold(negativeMargin) {
				negativeMargin = negativeMargin || this.options.negativeMargin;
				negativeMargin = (negativeMargin >= 0 ? negativeMargin * -1 : negativeMargin);
				var lastElement = this.getLastItem();
				// if the don't have a last element, the DOM might not have been loaded,
				// or the selector is invalid
				if (!lastElement) {
					return this.INFINITE;
				}
				return (lastElement.getBoundingClientRect().top + lastElement.offsetHeight + negativeMargin);
			}
			/**
			 * Returns current scroll offset for the given scroll container
			 * @param container
			 * @returns {number}
			 */
			getCurrentScrollOffset(container) {
				var scrollTop = 0,
					containerHeight;
				if (window === container) {
					containerHeight = window.innerHeight;
					scrollTop = this.getScrollTop();
				} else {
					containerHeight = container.offsetHeight;
					scrollTop = container.scrollTop;
				}
				// compensate for iPhone
				if (navigator.platform.indexOf("iPhone") != -1 || navigator.platform.indexOf("iPod") != -1) {
					containerHeight += 80;
				}
				return scrollTop;
			}
			/**
			 * Returns the url for the next page
			 */
			getNextUrl(container) {
				var self = this,
					currentUrl = window.location.href.replace(/#p=(\d?)/, ''),
					nextUrl,
					/* clone object data */
					params = Object.assign({}, self.options.data);
				params['ajaxscroll'] = 1;
				container = container || self.$container;
				if (self.nextPage < 0) return '';
				var nextElement = container.querySelectorAll(self.options.nextSelector);
				if (nextElement.length) {
					nextUrl = self.getLastElement(nextElement).getAttribute('href');
					if (self.getPageNumber(nextUrl) < self.nextPage) nextUrl = '';
				}
				if (!nextUrl) {
					var $pager = container.querySelectorAll(self.options.pagination);
					$pager = self.getPageUrl($pager);
					$pager.every(function (element, index) {
						var href = element.getAttribute('href'),
							page = self.getPageNumber(href) ? self.getPageNumber(href) : 1;
						if (self.nextPage == page) {
							nextUrl = href;
							return false;
						} else {
							return true;
						}
					});
				}
				if (!self.options.multi) {
					if (!nextUrl) {
						$pager = self.getPageUrl(container);
						$pager.every(function (element, index) {
							var href = element.getAttribute('href'),
								page = self.getPageNumber(href) ? self.getPageNumber(href) : 1;
							if (self.nextPage == page) {
								nextUrl = href;
								return false;
							} else {
								return true;
							}
						});
					}
					if (!nextUrl) {
						var customNext = document.querySelector(self.options.pagination);
						if (customNext) {
							nextUrl = customNext.getAttribute('data-next-url');
						}
					}
					if (!nextUrl) {
						var nextPage = self.scanPage(container);
						if (nextPage > 0) {
							params = Object.assign(params, { page: nextPage });
							nextUrl = currentUrl;
						} else {
							var $infinitescrollGrid = self.$container.querySelector('.infinitescroll-grid')
							if ($infinitescrollGrid) {
								if (self.nextPage <= $infinitescrollGrid.dataset.totalPage) {
									params = Object.assign(params, { page: self.nextPage });
									nextUrl = currentUrl;
								}
							}
						}
					}
				}
				if (nextUrl) {
					/* Update params, page .. */
					nextUrl = self.updateQueryParams(nextUrl, params);
				}

				return nextUrl ? nextUrl : '';
			}

			getPageUrl(elements) {
				var self = this,
					// regex: /.*(\?|&)p=\d*.*/
					regex = RegExp('.*(\\?|&)' + this.options.pageParam + '=\\d*.*');
				if (!this.is('a', elements)) {
					if (elements instanceof NodeList) {
						var elementsX = [];
						elements.forEach(function (element) {
							var links = element.querySelectorAll('a[href*="?' + self.options.pageParam + '="], a[href*="&' + self.options.pageParam + '="]');
							elementsX = elementsX.concat(Array.from(links));
						});
						elements = elementsX;
					} else {
						elements = elements.querySelectorAll('a[href*="?' + self.options.pageParam + '="], a[href*="&' + self.options.pageParam + '="]');
						elements = Array.from(elements);
					}
				}

				return Array.from(elements).filter(item => item.href.match(regex));
			}

			getPageNumber(url) {
				var page = this.getQueryParam(url, this.options.pageParam);
				return page ? parseInt(page) : null;
			}

			scanPage(container) {
				if (container instanceof HTMLDocument) {
					container = container.querySelector('body');
				}
				var outerHTML = container.outerHTML;
				var nextPage = this.nextPage;
				return outerHTML.includes('p=' + nextPage) ? nextPage : -1;
			}

			getQueryParam(href, param) {
				//this expression is to get the query strings
				let regx = new RegExp('[?&]' + param + '=([^&#]*)', 'i');
				let queryString = regx.exec(href);
				return queryString ? queryString[1] : null;
			}

			updateQueryParams(href, params = {}) {
				var self = this;
				for (const [param, value] of Object.entries(params)) {
					if (self.getQueryParam(href, param) != null) {
						let regx = new RegExp('[?&]' + param + '=([^&#]*)', 'i');
						href = href.replace(regx, function (match, val, offset, string) {
							return match.replace(val, encodeURIComponent(value));
						});
					} else {
						let paramStr = param + '=' + value;
						href += href.includes('?') ? '&' + paramStr : '?' + paramStr;
					}
				}
				return href;
			}

			removeQueryParam(href, param) {
				let regx = new RegExp('[?&]' + param + '=([^&#]*)', 'i');
				let isMulti = (href.includes('?') && href.includes('&'))
				href = href.replace(regx, function (match, val, offset, string) {
					return (match.includes('?') && isMulti) ? '?' : '';
				});
				return href.replace('?&', '?');
			}

			getPageHash(url) {
				let regex = new RegExp('#p=(\d?)');
				if (regex.test(url)) {
					let hash = url.match(/#p=(\d?)/);
					if (hash.length > 1) {
						return hash[1];
					}
				}
			}
			updatePageHash(url, value) {
				let regex = new RegExp('#p=(\d?)');
				if (regex.test(url)) {
					url = url.replace(/#p=(\d?)/, function (match, val, offset, string) {
						return match.replace(val, value);
					});
				} else {
					url = url + '#p=' + value;
				}
				return url;
			}
			getItems(dom) {
				var self = this,
					$itemContainer,
					items = [];
				$itemContainer = dom.querySelector(self.options.container);
				if ($itemContainer) {
					var $items = $itemContainer.querySelectorAll(self.options.item);
					if (!$items.length && self.options.smart) $items = $itemContainer.querySelectorAll(':scope > *');
					items = Array.from($items).filter(
						item => !(item.matches(self.options.itemRemove) || item.matches(self.options.pagination) || item.matches('magepow-infinitescroll'))
					);
				}
				return items;
			}

			/**
			 * Load the next page
			 */
			next() {
				var self = this;
				this.$container.addEventListener('loaded', function (event) {
					var dom = event.detail;
					if (dom.type != 'next') return;
					self.countNext++;
					self.nextPage++;
					self.nextUrl = self.getNextUrl(dom);
					var items = self.getItems(dom);
					if (typeof self.options.loaded === 'function') {
						self.options.loaded.bind(self)(dom, items);
					}
					self.render(items, 'next');
				}, { signal: this.signal });
			}

			/**
			 * Loads a page url
			 * @param url
			 */
			load(url, type) {
				var self = this,
					headers = new Headers();
				headers.append('pragma', 'no-cache');
				headers.append('cache-control', 'no-cache');
				self.$container.dispatchEvent(new CustomEvent('load', { detail: { url: url, type: type } }));
				fetch(`${url}`, { method: 'GET', headers: headers, cache: "no-cache" })
					.then((response) => response.text())
					.then((data) => {
						self.ajaxPage = self.getPageNumber(url);
						self.$domFetch = new DOMParser().parseFromString(data, "text/html");
						self.$domFetch.type = type;
						self.$container.dispatchEvent(new CustomEvent('loaded', { detail: self.$domFetch }));
					});
			}
			/**
			 * Renders items
			 * @param items
			 */
			render(items, type) {
				var self = this,
					ajaxPage = self.ajaxPage;
				items.forEach(function (item, idx) {
					item.classList.add(self.options.itemAjaxClass);
					item.dataset.page = ajaxPage;
					item.dataset.index = idx + 1;
					// if(!idx) item.classList.add('page-' + ajaxPage);
				});
				this.$container.dispatchEvent(new CustomEvent('render', { detail: items }));
				if (type == 'next') {
					var reference = this.getLastItem();
					items.forEach((item) => {
						/* Custom for Hyva Theme */
						if (window.hyva){
							hyva.activateScripts(item)
						}
						/* End custom for Hyva Theme */
						reference.insertAdjacentElement('afterend', item);
						reference = item;
					});
				} else {
					var reference = this.getFirstItem(),
						currentOffset = reference.getBoundingClientRect().top - self.getScrollTop();
					items.reverse().forEach((item) => {
						/* Custom for Hyva Theme */
						if (window.hyva) {
							hyva.activateScripts(item)
						}
						/* End custom for Hyva Theme */
						reference.insertAdjacentElement('beforebegin', item);
						reference = item;
					});
					// window.scrollTo({ top: reference.getBoundingClientRect().top - currentOffset, behavior: 'smooth' });
				}
				if (self.options.fadeIn) {
					var $itemsFadeIn = Array.from(items).filter(function (item) {
						if (self.is(self.options.itemHide, item)) {
							item.hidden = true;
							return false;
						}
						if (self.isHidden(item)) {
							return false;
						} else {
							return true;
						}
					});
					var countFadeIn = $itemsFadeIn.length;
					$itemsFadeIn.forEach(function (item, idx) {
						self.fadeIn(item, self.options.duration);
						if (++idx < countFadeIn) {
							return;
						}
						self.$container.dispatchEvent(new CustomEvent('rendered', { detail: { items: items, type: type } }));
					});
					if (!countFadeIn) self.$container.dispatchEvent(new CustomEvent('rendered', { detail: { items: items, type: type } }));
				} else {
					self.$container.dispatchEvent(new CustomEvent('rendered', { detail: { items: items, type: type } }));
				}
			}

			/**
			 * Hides the pagination
			 */
			hidePagination() {
				if (this.options.pagination != this.options.container) {
					var pagination = this.$container.querySelectorAll(this.options.pagination);
					if (pagination.length) {
						pagination.forEach(function (element) {
							if (!element.matches('body, section, .shopify-section, .infinitescroll-grid')) {
								element.style.display = 'none';
							}
						});
					}
				}
			}

			IASHistory() {
				var self = this,
					itemClasses = '.' + self.options.itemInitClass + ', .' + self.options.itemAjaxClass,
					classesLink = '.' + self.options.itemInitClass + ' a:not(.init)' + ', .' + self.options.itemAjaxClass + ' a:not(.init)',
					$items = self.initItems,
					historySatte = window.history.state,
					browser = self.getBrowser().toLowerCase();
				/* IASHistory */
				// Use if want config the same next
				// this.$container.addEventListener('scroll', self.onScroll.bind(this), { signal: this.signal });
				if (self.is('html', $items)) return;
				$items.forEach((item, idx) => {
					item.classList.add(self.options.itemInitClass);
					item.dataset.page = self.currentPage;
					item.dataset.index = idx + 1;
				});

				var CustomInview = function (e) {
					if (self.getScrollTop() == 0) {
						var triggerPrev = document.querySelector('.ias-trigger-prev');
						if (window.boostSDAppConfig || window.boostSDTaeUtils || window.boostSDData) {
						} else {
							if (triggerPrev) triggerPrev.click();
						}
					}
					var boostCommerce = document.querySelector('.boost-sd__product-list'),
						usfLoadMore = document.querySelector('.usf-load-more, .boost-sd__pagination-button--next');
					if (usfLoadMore) {
						if (boostCommerce) {
							self.$container.dispatchEvent(new Event('showSpinnerNext'));
						}
						if (self.isInViewport(usfLoadMore)) {
							usfLoadMore.click();
						}
					} else {
						if (boostCommerce) {
							self.$container.dispatchEvent(new Event('hideSpinnerNext'));
							return false;
						}
					}
					var items = document.querySelectorAll(itemClasses);
					var visibleItems = Array.from(items).filter(function (item) {
						return getComputedStyle(item).display !== 'none';
					});
					var $inView = self.getInViewport(visibleItems);
					if ($inView.length) {
						var inView = self.getFirstElement($inView),
							inPage = inView.dataset.page;
						self.currentPage = inPage;
						if (!self.options.multi && self.options.autoLink) {
							var inOffset = inView.dataset.index,
								url = window.location.href.replace('#', ''),
								page = self.getQueryParam(url, self.options.pageParam);
							if (inPage == page) return;
							let pageParam = {};
							pageParam[self.options.pageParam] = inPage;
							if (inPage > 1) {
								url = self.updateQueryParams(url, pageParam);
							} else {
								url = self.removeQueryParam(url, self.options.pageParam);
							}
							let state = Object.assign(pageParam, { ajaxscroll: true, resetPage: self.options.resetPage, offset: inOffset }); 
							window.history.replaceState(state, null, url);
						}
					}
				}

				self.$scrollContainer.addEventListener('scroll', CustomInview, { signal: this.signal });
				if ("orientation" in window.screen) {
					window.screen.orientation.addEventListener("change", CustomInview, { signal: this.signal });
				}
				if (browser.includes('firefox')) return;
				document.addEventListener("click", function (event) {
					var target = event.target;
					if (!target.closest(classesLink)) return;
					var element = self.is('a', target) ? target : target.closest('a'),
						url = window.location.href.replace('#', '');
					// event.preventDefault();
					if (element.href) {
						window.location.hash = '';
						var item = target.closest(itemClasses),
							inPage = item.dataset.page,
							offset = 1;
						if (item.dataset.index) {
							offset = item.dataset.index;
						} else {
							var items = Array.from(item.parentElement.children).filter(function (child, idx) {
								return !child.classList.contains(self.options.itemAjaxClass);
							});
							items.forEach(function (child, idx) {
								if (child === item) {
									offset = idx + 1;
								}
							});
						}
						if (inPage == 1) {
							url = self.removeQueryParam(url, self.options.pageParam);
						} else {
							let params = {};
							params[self.options.pageParam] = inPage;
							url = self.updateQueryParams(url, params);
						}
						window.history.replaceState({ ajaxscroll: true, page: inPage, resetPage: false, offset: offset }, null, url);
						element.classList.add('init');
					}
				}, { signal: this.signal });
			}

			/* IASTrigger */
			IASTrigger() {
				var self = this;
				this.countNext = 0;
				this.countPrev = 0;
				this.htmlTriggerNext = (this.options.htmlTriggerNext).replace('{text}', this.options.textTrigger);
				this.htmlTriggerPrev = (this.options.htmlTriggerPrev).replace('{text}', this.options.textTriggerPrev);
				this.$container.addEventListener('ready', function () {
					self.next.bind(self)();
					self.prev.bind(self)();
					self.createTrigger.bind(self)();
				}, { signal: this.signal });
				this.$container.addEventListener('rendered', function (event) {
					self.bind();
					var items = event.detail.items,
						type = event.detail.type;
					if (type == 'next') {
						self.$container.dispatchEvent(new Event('hideSpinnerNext'));
					}
					if (type == 'prev') {
						self.$container.dispatchEvent(new Event('hideSpinnerPrev'));
					}
					if (self.nextUrl) {
						self.$container.dispatchEvent(new Event('showTriggerNext'));
					} else {
						self.$container.dispatchEvent(new Event('showNoneLeft'));
					}
					if (typeof self.options.rendered === 'function') {
						self.options.rendered.bind(self)(items);
					}
				}, { signal: this.signal });
			}
			/**
			 * Create button trigger
			 */
			createTrigger() {
				var self = this,
					firstItem = this.getFirstItem(),
					parentNext = document.querySelector(this.options.nextAppendTo) || firstItem ? firstItem.parentElement : this.$itemsContainer,
					parentPrev = document.querySelector(this.options.prevPrependTo) || firstItem ? firstItem.parentElement : this.$itemsContainer;
				this.buttonNext = this.getTrigger(this.htmlTriggerNext, 'next');
				this.buttonPrev = this.getTrigger(this.htmlTriggerPrev, 'prev');
				this.buttonNext.style.display = 'none';
				this.buttonPrev.style.display = 'none';
				if (parentNext) {
					parentNext.after(this.buttonNext);
					this.$container.addEventListener('showTriggerNext', function () {
						self.showTrigger('next');
					}, { signal: this.signal });
					this.$container.addEventListener('hideTriggerNext', function () {
						self.hideTrigger('next');
					}, { signal: this.signal });
				}
				if (parentPrev) {
					parentPrev.before(this.buttonPrev);
					this.$container.addEventListener('showTriggerPrev', function () {
						self.showTrigger('prev');
					}, { signal: this.signal });
					this.$container.addEventListener('hideTriggerPrev', function () {
						self.hideTrigger('prev');
					}, { signal: this.signal });
				}
				this.buttonNext.addEventListener('click', this.triggerClick.bind(this), { signal: this.signal });
				this.buttonPrev.addEventListener('click', this.triggerClick.bind(this), { signal: this.signal });
			}
			/**
			 * @returns {Element}
			 * @param {string} html
			 */
			getTrigger(content, type) {
				var content = content || 'Text HTML here',
					id = 'ias_trigger_' + type + '_' + this.uid,
					trigger = document.querySelector('#' + id);
				if (!trigger) {
					const dom = new DOMParser().parseFromString(content, "text/html");
					trigger = dom.body.firstElementChild;
					trigger.type = type;
					trigger.classList.add('ias-trigger', 'ias-trigger-' + type);
					trigger.id = id;
				}
				return trigger;
			}
			/**
			 * Hide Trigger
			 */
			hideTrigger(type) {
				var element = this.$container.querySelector('.ias-trigger-' + type);
				if (element) element.style.display = 'none';
			};
			/**
			 * Show Trigger
			 */
			showTrigger(type) {
				if (this.INFINITE == this.offsetTrigger) return;
				if (type == 'next' && this.countNext < this.offsetTrigger) {
					this.$container.dispatchEvent(new Event('hideTriggerNext'));
					return true;
				} else if (type == 'prev' && this.countNext < this.offsetTrigger) {
					this.$container.dispatchEvent(new Event('hideTriggerPrev'));
					return true;
				}
				this.unbind();
				this.$container.dispatchEvent(new Event('hideNoneLeft'));
				this.$container.dispatchEvent(new Event('hideSpinner' + this.capitalizeFirstLetter(type)));
				var element = this.$container.querySelector('.ias-trigger-' + type);
				if (element) element.style.display = '';
			};

			triggerClick(event) {
				if (!this.options.plan) {
					if ((this.currentPage > this.options.offset) && !event.isTrusted) return;
				}
				var element = event.currentTarget;
				element.style.display = 'none';
				if (element.type == 'next') {
					if (this.nextUrl) {
						this.unbind();
						this.$container.dispatchEvent(new Event('next'));
						if (typeof this.options.load === 'function') this.options.load.bind(this)('next');
						this.load(this.nextUrl, 'next');
					} else {
						if (this.currentPage > 1) this.$container.dispatchEvent(new Event('showNoneLeft'));
					}
				} else {
					if (this.prevUrl) {
						this.unbind();
						this.$container.dispatchEvent(new Event('prev'));
						if (typeof this.options.load === 'function') this.options.load.bind(this)('prev');
						this.load(this.prevUrl, 'prev');
					} else {
						if (this.ajaxPage == 2) this.$container.dispatchEvent(new Event('showNoneLeftPrev'));
					}
				}
			}

			/* IASSpinner */
			IASSpinner() {
				var self = this;
				this.spinnerHtml = (this.options.htmlSpinner).replace('{src}', this.options.src);
				this.$container.addEventListener('ready', this.createSpinner.bind(this), { signal: this.signal });
				this.$container.addEventListener('load', function (event) {
					if (event.detail.type == 'next') {
						self.$container.dispatchEvent(new Event('showSpinnerNext'));
					} else {
						self.$container.dispatchEvent(new Event('showSpinnerPrev'));
					}
				}, { signal: this.signal });
			}
			/**
			 * Shows spinner
			 */
			createSpinner() {
				var self = this,
					firstItem = this.getFirstItem(),
					parentNext = document.querySelector(this.options.nextAppendTo) || firstItem ? firstItem.parentElement : this.$itemsContainer,
					parentPrev = document.querySelector(this.options.prevPrependTo) || firstItem ? firstItem.parentElement : this.$itemsContainer;
				this.spinnerNext = this.getSpinner(this.spinnerHtml, 'next');
				this.spinnerPrev = this.getSpinner(this.spinnerHtml, 'prev');
				this.spinnerNext.style.display = 'none';
				this.spinnerPrev.style.display = 'none';
				if (parentNext) {
					parentNext.after(this.spinnerNext);
					this.$container.addEventListener('showSpinnerNext', function () {
						self.showSpinner('next');
					}, { signal: this.signal });
					this.$container.addEventListener('hideSpinnerNext', function () {
						self.hideSpinner('next');
					}, { signal: this.signal });
				}
				if (parentPrev) {
					parentPrev.before(this.spinnerPrev);
					this.$container.addEventListener('showSpinnerPrev', function () {
						self.showSpinner('prev');
					}, { signal: this.signal });
					this.$container.addEventListener('hideSpinnerPrev', function () {
						self.hideSpinner('prev');
					}, { signal: this.signal });
				}
			};

			/**
			 * Show spinner
			 */
			showSpinner(type) {
				this.$container.dispatchEvent(new Event('hideTrigger' + this.capitalizeFirstLetter(type)));
				var element = this.$container.querySelector('.ias-spinner-' + type);
				if (element) element.style.display = '';
			};
			/**
			 * Hide spinner
			 */
			hideSpinner(type) {
				var element = this.$container.querySelector('.ias-spinner-' + type);
				if (element) element.style.display = 'none';
			};
			/**
			 * @returns {Element}
			 */
			getSpinner(content, type) {
				var content = content || 'Text HTML here',
					id = 'ias_spinner_' + type + '_' + this.uid,
					spinner = document.querySelector('#' + id);
				if (!spinner) {
					const dom = new DOMParser().parseFromString(content, "text/html");
					spinner = dom.body.querySelector(':scope > *');
					spinner.type = type;
					spinner.classList.add('ias-spinner', 'ias-spinner-' + type);
					spinner.id = id;
				}
				return spinner;
			};

			/* IASNoneLeft */
			IASNoneLeft() {
				this.htmlNoneLeft = (this.options.htmlNoneLeft).replace('{text}', this.options.textNoneLeft);
				this.$container.addEventListener('showNoneLeft', this.showNoneLeft.bind(this), { signal: this.signal });
				this.$container.addEventListener('hideNoneLeft', this.hideNoneLeft.bind(this), { signal: this.signal });
			}
			/**
			 * Shows none left message
			 */
			showNoneLeft() {
				var lastItem = this.getLastItem(),
					id = '#ias_noneleft_' + this.uid,
					element = this.$container.querySelector(id) || this.createNoneLeft(),
					parent = document.querySelector(this.options.nextAppendTo) || lastItem ? lastItem.parentElement : this.$itemsContainer;
				this.$container.dispatchEvent(new Event('hideTriggerNext'));
				element.style.display = '';
				if (parent) parent.after(element);
			}
			/**
			 * Hide none left message
			 */
			hideNoneLeft() {
				var id = '#ias_noneleft_' + this.uid,
					element = this.$container.querySelector(id);
				if (element) element.style.display = 'none';
			}
			/**
			 * @returns {Element}
			 */
			createNoneLeft() {
				var self = this,
					id = 'ias_noneleft_' + this.uid,
					dom = new DOMParser().parseFromString(this.htmlNoneLeft, "text/html"),
					noneleft = dom.body.querySelector(':scope > *');
				noneleft.classList.add('ias-noneleft');
				noneleft.id = id;
				noneleft.addEventListener('click', function () {
					var infinitescrollGrid = self.$container.querySelector('.infinitescroll-grid'),
						position = 'start';
					if (infinitescrollGrid) {
						var previous = infinitescrollGrid.previousElementSibling;
						if (previous && self.isVisible(previous)) {
							infinitescrollGrid = previous;
							// position = 'end';
						}
						window.scrollTo({
							behavior: 'smooth',
							top: infinitescrollGrid.getBoundingClientRect().top + window.pageYOffset - 150
						});
					}
				}, { signal: this.signal });
				return noneleft;
			}
			/**
			 * @param currentScrollOffset
			 */
			onScroll(event) {
				if (!this.prevUrl) return;
				var currentScrollOffset = event.detail.currentScrollOffset,
					firstItemScrollThreshold = this.getScrollThresholdFirstItem();
				currentScrollOffset -= (this.$scrollContainer == window) ? this.$scrollContainer.innerHeight : this.$scrollContainer.clientHeight;
				if (currentScrollOffset <= firstItemScrollThreshold) {
					if (window.boostSDAppConfig || window.boostSDTaeUtils || window.boostSDData) {

					} else {
						if (this.buttonPrev) this.buttonPrev.click();
					}
				}
			}
			/**
			 * Returns the url for the prev page
			 */
			getPrevUrl(container) {
				/* clone object data */
				var self = this,
					prevUrl,
					params = Object.assign({}, self.options.data);
				params['ajaxscroll'] = 1;
				if (!container) {
					container = self.$container;
				}
				if (this.prevPage < 0) return '';
				var prevElement = container.querySelector(self.options.prevSelector);
				if (prevElement) {
					prevUrl = prevElement.getAttribute("href");
				}
				if (!prevUrl) {
					var $pager = container.querySelectorAll(self.options.pagination);
					$pager = self.getPageUrl($pager);

					$pager.every(function (element, index) {
						var href = element.getAttribute('href'),
							page = self.getPageNumber(href) ? self.getPageNumber(href) : 1;
						if (self.prevPage == page) {
							prevUrl = href;
							return false;
						} else {
							return true;
						}
					});
					if (!prevUrl) {
						$pager = self.getPageUrl(container);
						$pager.every(function (element, index) {
							var href = element.getAttribute('href'),
								page = self.getPageNumber(href);
							if (self.prevPage == page) {
								prevUrl = href;
								return false;
							} else {
								return true;
							}
						});
					}
					if (!prevUrl) {
						var customNext = document.querySelector(this.options.pagination);
						if (customNext) {
							prevUrl = customNext.getAttribute('custom-prev-page');
						}
					}
					if (!prevUrl && this.prevPage > 1) {
						let pageParam = {};
						pageParam[this.options.pageParam] = this.prevPage - 1;
						params = Object.assign(params, pageParam);
						prevUrl = window.location.href.replace(/#p=(\d?)/, '');
					}
				}
				if (prevUrl) {
					prevUrl = this.updateQueryParams(prevUrl, params);
				} else {
					this.prevPage = -1;
				}
				return prevUrl ? prevUrl : '';
			}
			/**
			 * Returns scroll threshold. This threshold marks the line from where
			 * IAS should start loading the next page.
			 * @return {number}
			 */
			getScrollThresholdFirstItem() {
				var firstElement = this.getFirstItem();
				if (!firstElement) {
					return this.INFINITE;
				}
				return (firstElement.offsetTop);
			}
			/**
			 * Load the prev page
			 */
			prev() {
				var self = this;
				this.$container.addEventListener('loaded', function (event) {
					var dom = event.detail;
					if (dom.type != 'prev') return;
					self.countPrev++;
					self.prevPage--;
					self.prevUrl = self.getPrevUrl(dom);
					var items = self.getItems(dom);
					if (typeof self.options.loaded === 'function') {
						self.options.loaded.bind(self)(dom, items);
					}
					self.render(items, 'prev');
				}, { signal: this.signal });
			};

		}

		if (typeof MagepowAppsScroll === 'undefined') {
			class MagepowAppsScroll extends elementX {
				constructor() {
					super();
					this.settings = {
						autoLink: true,
						offset: -1,
						loadMoreButtonText: "Load More",
						doneText: "You've reached the end of the item."
					};
					this.options = {
						section: 'body',
						sectionSelector: '.main',
						container: '.products-grid',
						pagination: '.pagination-wrapper, .Pagination, #pagination, .pagination, .pagination__list, .pagination-custom, .page-numbers, [data-paginate="number"], .paginate, .boost-pfs-filter-bottom-pagination',
						nextSelector: '.pagination .next, .page-next',
					};
					this.identifier = '#magepowapps-infinitescroll-settings, collection-infinitescroll script.settings';
					this.developer = false;
					this.init();
				}

				init() {
					var self = this;
					this.shop = { plan: sessionStorage.getItem('infinite_plan') };
					if (this.developer) console.group('MagepowApps Developer');
					var $settings = document.querySelector(self.identifier);
					if ($settings) {
						if (document.currentScript && document.currentScript.src) {
							self.getAppVersion(document.currentScript.src.split('/js/')[0].split('/').pop(), 'https://magepow.com/magento-2-infinite-scroll.html');
						}
						if ($settings.getAttribute('data-item-count') == 1) return;
						this.settings = JSON.parse($settings.innerHTML) || {};
						if (!this.settings.src) {
							var svgLoader = document.querySelector("#magepowapp-infinitescroll-loader, collection-infinitescroll script.loader"),
								svgLoaderHtml = document.createElement('div');
							svgLoaderHtml.innerHTML = svgLoader.innerHTML;
							svgLoader = svgLoaderHtml.querySelector('svg');
							svgLoader = new XMLSerializer().serializeToString(svgLoader);
							svgLoader = 'data:image/svg+xml;base64,' + window.btoa(svgLoader);
							Object.assign(this.settings, { 'src': svgLoader });
						}
						if (!$settings.dataset.page_type) {
							if (window.location.pathname.includes('/a/search/')) {
								$settings.dataset.page_type = 'cloudsearch';
							} else {
								$settings.dataset.page_type = 'x';
							}
						}
						if ($settings.dataset.plan) {
							self.shop.plan = $settings.dataset.plan;
						}
						Object.assign(this.settings, { plan: self.shop.plan, page_type: $settings.dataset.page_type });
						document.documentElement.classList.add('page-' + this.settings.page_type);
						if (!self.shop.plan || self.shop.plan === 'null' || self.shop.plan < 1) {
							if ((this.settings.offset > 1 || this.settings.offset == -1 || this.settings.offset === false)) {
								Object.assign(this.settings, { offset: 1 });
							}
						}
						/* delete empty config */
						var requireConfig = ['item', 'container', 'pagination', 'nextSelector', 'nextAppendTo', 'itemHide'];
						requireConfig.forEach(function (val) {
							if (self.settings[val] == '' || self.settings[val] == 'Auto') delete self.settings[val];
						});
					} else {
						self.logMsg('Inject InfiniteScroll for mode Developer!');
					}
					const offset = new URLSearchParams(window.location.search).get('offset');
					if (offset) {
						Object.assign(this.settings, { offset: parseFloat(offset) });
					}
					this.settings = Object.assign({}, this.options, this.settings) || {};
					this.initScroll();
					document.body.addEventListener("infinitescroll:show:page", function (event) {
						self.showPage();
					});
				}

				initScroll() {
					
					var self = this,
						config = Object.assign({}, self.options, self.settings),
						options = Object.assign({}, config, {
							multi: false,
							htmlSpinner: "<div><img src=\"{src}\"/><span><em>" + config["loadingText"] + "</em></span></div>",
							/* IASNoneLeft */
							textNoneLeft: config["doneText"],
							/* IASTrigger */
							textTrigger: config["loadMoreButtonText"],
							textTriggerPrev: config["prevMoreButtonText"],
						});
					document.body.addEventListener('collectionUpdated', function () {
						document.body.classList.add('infinitescroll-pro');
						document.body.dispatchEvent(new Event('infinitescroll:init:before'));
						var elements = options.multi ? document.querySelectorAll(self.settings.sectionSelector) : document.querySelectorAll('body');
						elements.forEach(function (scrollX) {
							scrollX.classList.add('infinitescroll-init');
							/* Custom for Hyva Theme */
							if (window.hyva) {
								options = Object.assign(options, {
									container: '.products-grid > ul',
									item: ':scope > *:not([id^="ias_spinner_"])',
								});
							}
							/* End custom for Hyva Theme */
							var containerX = scrollX.querySelector(options.container),
								itemX = containerX.querySelector(options.item);
							if (itemX) containerX = itemX.parentElement;
							containerX.classList.add('infinitescroll-grid');
							if (containerX.lastElementChild) containerX.lastElementChild.insertAdjacentHTML('afterend', '<magepow-infinitescroll style="display:none">🚀</magepow-infinitescroll>');

							self.createIAS(scrollX, options);
						});
					});
					if (/complete|interactive|loaded/.test(document.readyState)) {
						document.body.dispatchEvent(new Event('collectionUpdated'));
					} else {
						document.addEventListener('DOMContentLoaded', function () {
							document.body.dispatchEvent(new Event('collectionUpdated'));
						}, false);
					}
				}

				createIAS(element, options) {
					var self = this,
						uid,
						ias = element.ias;
					if (ias) {
						uid = ias.uid
						ias.refresh();
						// element.ias = null;
					}
					element.ias = new IAS(element,
						Object.assign({}, options, {
							uid: uid ? uid : (new Date()).getTime(),
							shopify: window.Shopify,
							tryNext: function () {
								if (window.boostSDAppConfig) {
									/* Compatible with https://apps.shopify.com/product-filter-search */
									return true;
								}
								return options.tryNext;
							}(),
							ready: function () {
								var items = this.initItems;
								if (this.is('[class*=aos], [data-aos]', items) || this.isContains('[class*=aos], [data-aos]', items)) {
									this.aosAnimate = true;
								} else if (this.is('[data-cc-animate]', items) || this.isContains('[data-cc-animate]', items)) {
									this.ccAnimate = true;
								} else if (this.is('[reveal-on-scroll]', items)) {
									this.revealOnScroll = true;
								} else if (this.is('.animation--item-revealed', items)) {
									this.animationItemRevealed = true;
								}
								var links = [];
								items.forEach((item) => {
									item.querySelectorAll('a')
								});
								document.dispatchEvent(new CustomEvent('ias:ready', { detail: { ias: this } }));
								/* Log object IAS to debug */
								self.loggerDeveloper({ 'Magepow InfiniteScroll instance': this });
							},
							load: function (type) {
								if (self.themecfg && self.themecfg.hasOwnProperty('load')) {
									if (typeof self.themecfg.load === 'function') {
										if (type == 'next') {
											this.nextUrl = self.themecfg.load(this.nextUrl);
										} else {
											this.prevUrl = self.themecfg.load(this.prevUrl);
										}
									} else {
										self.themecfg.load;
									}
								}
							},
							loaded: function (resultDom, items) {
								var pagination = resultDom.querySelector(this.options.pagination);
								if (pagination) {
									var docPagination = document.querySelector(this.options.pagination);
									if (docPagination && !this.is('.infinitescroll-grid', docPagination)) {
										docPagination.innerHTML = pagination.innerHTML;
									}
								}
								document.body.dispatchEvent(new CustomEvent('ias:loaded', { detail: { result: resultDom, items: items } }));
							},
							rendered: function (items) {
								if (this.aosAnimate) {
									items.forEach(item => {
										if (item.matches('[class*=aos], [data-aos]')) item.classList.add('aos-init', 'aos-animate');
										item.querySelectorAll('[class*=aos], [data-aos]').forEach(el => {
											el.classList.add('aos-init', 'aos-animate');
										});
									});
								} else if (this.ccAnimate) {
									items.forEach(item => {
										if (item.matches('[data-cc-animate]')) {
											item.classList.add('cc-animate-init', '-in', 'cc-animate-complete');
											item.setAttribute('data-cc-animate', '');
										}
										item.querySelectorAll('[data-cc-animate]').forEach(el => {
											el.classList.add('cc-animate-init', '-in', 'cc-animate-complete');
											el.setAttribute('data-cc-animate', '');
										});
									});
								} else if (this.revealOnScroll) {
									items.forEach((item) => {
										item.setAttribute('reveal-on-scroll', null);
									});
								} else if (this.animationItemRevealed) {
									items.forEach((item) => {
										item.classList.add('animation--item-revealed');
									});
								}

								/* Call back other extension */
								
								document.body.dispatchEvent(new CustomEvent('contentUpdated', { bubbles: true, cancelable: true, detail: items }));

								/* Compatible with other extension */
								/* Support Magefan_Blog */
								var el, url,
									items = document.getElementsByClassName('mfblogunveil');
								if (items.length) {
									for (var i = 0; i < items.length; i++) {
										el = items[i];
										url = el.getAttribute('data-original');
										if (!url) {
											continue;
										};
										if ('IMG' == el.tagName) {
											el.src = url;
										} else {
											el.style.backgroundImage = `url('${url}')`;
										}
									}
								}

								/* End Magefan_Blog */
								
							}
						})
					);
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

			new MagepowAppsScroll();
		}

	})()
} catch (e) {
	document.body.classList.add('infinitescroll-off');
	document.body.classList.remove('infinitescroll-pro');
	console.error(e);
}