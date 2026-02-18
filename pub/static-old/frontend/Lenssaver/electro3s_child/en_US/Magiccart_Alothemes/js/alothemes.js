/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magepow.com/) 
 * @license 	https://www.magepow.com/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2014-04-25 13:16:48
 * @@Modify Date: 2021-05-12 12:05:30
 * @@Function:
 */

define([
    'jquery',
    'gridSlider',
    'notifySlider',
	'magiccart/parallax',
	'mage/cookies',
	'Magento_Ui/js/modal/modal'
    ], function ($, gridSlider, notifySlider, parallax, cookie, modal) {
	"use strict";

	function getAppVersion(appName, href) {
		// this expression is to get the version string
		let regx = new RegExp('.*\/(.*?)\/assets\/', 'i');
		let result = regx.exec(href);
		let version = result ? result[1].replace(/\D/g, '') : '1.0.0';
		console.log(`%c ${appName} %c v${version}  %c`, "background: #555555; padding: 1px; margin-bottom: 2px; border-radius: 3px 0 0 3px; color: #fff", "background: #44cc11; padding: 1px; margin-bottom: 2px; border-radius: 0 3px 3px 0; color: #fff", "background:transparent", `🚀 ${href}`);

		return version;
	}

	if (!document.documentElement.classList.contains('alothemes-init')) {
		document.documentElement.classList.add('alothemes-init');
		getAppVersion('Magiccart_Alothemes', 'https://alothemes.com');
	}

	if (!customElements.get("trigger-modal")) {
		class TriggerModal extends HTMLElement {
			constructor() {
			    super();
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
					target = document.querySelector(config.target) || self.querySelector('template'),
			        	options = self.datasetToObject(config.options) || {};
					if (target instanceof HTMLTemplateElement) {
						target = target.content.cloneNode(true);
					}
			        if(target) modal( Object.assign(options, {
						autoOpen: true,
						opened: function () {
							$('body').trigger('contentUpdated');
							document.documentElement.classList.add('open-modal');
						},
						closed: function () {
							document.documentElement.classList.remove('open-modal');
						}
					}), target);
			    });
			}
		}
		customElements.define("trigger-modal", TriggerModal);
	}

	window.magicproduct = function(el, iClass) {
		if(!el.length) return;
		if(!el.hasClass('grid-slider')) el.addClass('grid-slider');
		el.parent().gridSlider();
	};
	/* Tab bootstrap 3.4.1 */
	+function ($) {
	  'use strict';

	  // TAB CLASS DEFINITION
	  // ====================

	  var Tab = function (element) {
	    // jscs:disable requireDollarBeforejQueryAssignment
	    this.element = $(element)
	    // jscs:enable requireDollarBeforejQueryAssignment
	  }

	  Tab.VERSION = '3.4.1'

	  Tab.TRANSITION_DURATION = 150

	  Tab.prototype.show = function () {
	    var $this    = this.element
	    var $ul      = $this.closest('ul:not(.dropdown-menu)')
	    var selector = $this.data('target')

	    if (!selector) {
	      selector = $this.attr('href')
	      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
	    }

	    if ($this.parent('li').hasClass('active')) return

	    var $previous = $ul.find('.active:last a')
	    var hideEvent = $.Event('hide.bs.tab', {
	      relatedTarget: $this[0]
	    })
	    var showEvent = $.Event('show.bs.tab', {
	      relatedTarget: $previous[0]
	    })

	    $previous.trigger(hideEvent)
	    $this.trigger(showEvent)

	    if (showEvent.isDefaultPrevented() || hideEvent.isDefaultPrevented()) return

	    var $target = $(document).find(selector)

	    this.activate($this.closest('li'), $ul)
	    this.activate($target, $target.parent(), function () {
	      $previous.trigger({
	        type: 'hidden.bs.tab',
	        relatedTarget: $this[0]
	      })
	      $this.trigger({
	        type: 'shown.bs.tab',
	        relatedTarget: $previous[0]
	      })
	    })
	  }

	  Tab.prototype.activate = function (element, container, callback) {
	    var $active    = container.find('> .active')
	    var transition = callback
	      && $.support.transition
	      && ($active.length && $active.hasClass('fade') || !!container.find('> .fade').length)

	    function next() {
	      $active
	        .removeClass('active')
	        .find('> .dropdown-menu > .active')
	        .removeClass('active')
	        .end()
	        .find('[data-toggle="tab"]')
	        .attr('aria-expanded', false)

	      element
	        .addClass('active')
	        .find('[data-toggle="tab"]')
	        .attr('aria-expanded', true)

	      if (transition) {
	        element[0].offsetWidth // reflow for transition
	        element.addClass('in')
	      } else {
	        element.removeClass('fade')
	      }

	      if (element.parent('.dropdown-menu').length) {
	        element
	          .closest('li.dropdown')
	          .addClass('active')
	          .end()
	          .find('[data-toggle="tab"]')
	          .attr('aria-expanded', true)
	      }

	      callback && callback()
	    }

	    $active.length && transition ?
	      $active
	        .one('bsTransitionEnd', next)
	        .emulateTransitionEnd(Tab.TRANSITION_DURATION) :
	      next()

	    $active.removeClass('in')
	  }


	  // TAB PLUGIN DEFINITION
	  // =====================

	  function Plugin(option) {
	    return this.each(function () {
	      var $this = $(this)
	      var data  = $this.data('bs.tab')

	      if (!data) $this.data('bs.tab', (data = new Tab(this)))
	      if (typeof option == 'string') data[option]()
	    })
	  }

	  var old = $.fn.tab

	  $.fn.tab             = Plugin
	  $.fn.tab.Constructor = Tab


	  // TAB NO CONFLICT
	  // ===============

	  $.fn.tab.noConflict = function () {
	    $.fn.tab = old
	    return this
	  }


	  // TAB DATA-API
	  // ============

	  var clickHandler = function (e) {
	    e.preventDefault()
	    Plugin.call($(this), 'show')
	  }

	  $(document)
	    .on('click.bs.tab.data-api', '[data-toggle="tab"]', clickHandler)
	    .on('click.bs.tab.data-api', '[data-toggle="pill"]', clickHandler)

	}($);

	/* Timer */
	(function ($) {
		"use strict";
		$.fn.timer = function (options) {
			var defaults = {
				classes  	 : '.countdown',
				layout	 	 : '<span class="number day">%%D%%</span><span class class="colon">:</span><span class="number hour">%%H%%</span><span class="colon">:</span><span class="number min">%%M%%</span><span class="colon">:</span><span class="number sec">%%S%%</span>',
				layoutcaption: '<div class="timer-box"><span class="number day">%%D%%</span><span class="title">Days</span></div><div class="timer-box"><span class="number hour">%%H%%</span><span class="title">Hrs</span></div><div class="timer-box"><span class="number min">%%M%%</span><span class="title">Mins</span></div><div class="timer-box"><span class="number sec">%%S%%</span><span class="title">Secs</span></div>',
				leadingZero	 : true,
				countStepper : -1, // s: -1 // min: -60 // hour: -3600
				timeout	 	 : '<span class="timeout">Time out!</span>',
			};

			var settings = $.extend(defaults, options),
				layout			 = settings.layout,
				layoutcaption	 = settings.layoutcaption,
				leadingZero 	 = settings.leadingZero,
				countStepper 	 = settings.countStepper,
				setTimeOutPeriod = (Math.abs(countStepper)-1)*1000 + 990,
				timeout 		 = settings.timeout;

			var methods = {
				init : function() {
					return this.each(function() {
						var $countdown 	= $(settings.classes, $(this));
						if( $countdown.length && !$countdown.hasClass('init')){
							$countdown.addClass('init');
							methods.timerLoad($countdown);
						}
					});
				},
				
				timerLoad: function(el){
					var gsecs = el.data('timer');
					if(isNaN(gsecs)){
						var start = Date.parse(new Date());
						var end = Date.parse(gsecs);
						gsecs  = (end - start)/1000;	
					}
                    if (gsecs > 0) {
                        var isLayout = el.find('.min');
                        if (!isLayout.length) {
                            if (el.hasClass('caption'))  el.html(layoutcaption);
                            else el.html(layout);;
                        }
                        methods.CountBack(el, gsecs);
                    }
				},

				calcage: function (secs, num1, num2) {
					var s = ((Math.floor(secs/num1)%num2)).toString();
					if (leadingZero && s.length < 2) s = "0" + s;
					return "<b>" + s + "</b>";
				},

				CountBack: function (el, secs) {
	                var countInterval = setInterval(function count() {
	                    if (secs < 0) {
	                        clearInterval(countInterval);
	                        el.html(timeout);
	                        return;
	                    }
						el.find('.day').html(methods.calcage(secs,86400,100000));
						el.find('.hour').html(methods.calcage(secs,3600,24));
						el.find('.min').html(methods.calcage(secs,60,60));
						el.find('.sec').html(methods.calcage(secs,1,60));
	                    secs += countStepper;
	                    return count;
	                }(), setTimeOutPeriod);
				},

			};

			if (methods[options]) { // $("#element").pluginName('methodName', 'arg1', 'arg2');
				return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
			} else if (typeof options === 'object' || !options) { // $("#element").pluginName({ option: 1, option:2 });
				return methods.init.apply(this);
			} else {
				$.error('Method "' + method + '" does not exist in timer plugin!');
			}
		}

		$(function($) {
			var countdown = $('.alo-count-down').not('.exception');
			if (typeof alo_timer_layoutcaption != 'undefined'){
				countdown.timer({layout : alo_timer_layout, layoutcaption : alo_timer_layoutcaption, timeout : alo_timer_timeout });
			    $('body').on('contentUpdated', function () {
					$('.alo-count-down').not('.exception').timer({layout : alo_timer_layout, layoutcaption : alo_timer_layoutcaption, timeout : alo_timer_timeout });
			    });
			} else {
				countdown.timer();			
			}
		});
	})($);
	/* End Timer */

	$(function($) {
		$('body').removeClass('preload');
		var specialOffer = $('#header-offer');
		specialOffer.find('.header-offer-close').on('click', function() {
			specialOffer.slideUp('slow');
		});

		/* Fixed error header mobile Alothemes */
		$('.alo-account >.header.links').clone().appendTo('#store\\.links');

		$('.home-slider, .magicslider').each(function() { // home-slider, magic-slider
			if($(this).hasClass('autoplay')){
	    		magicproduct($(this));
	    	}
		});

		// Realated + Upsell + Crosssell
	    $('.products-related .product-items, .products-upsell .product-items, .products-crosssell .product-items').each(function() {
	    	magicproduct($(this))
	    });
		// End Realated + Upsell + Crosssell

		// add Js
		var $toggleTab  = $('.toggle-tab');
		var $toggleContent  = $('.toggle-content');
		$(document).on("click", '.toggle-tab', function(){
			$(this).parent().toggleClass('toggle-visible').find('.toggle-content').toggleClass('visible');
			var input = $(this).parent().find('input');
			if(input.length) input.first().focus();
		});
		$(window).on('click', function(event) {
			if (!$toggleContent.is(event.target) && $toggleContent.has(event.target).length === 0) 
			{
				$toggleTab.each(function() {
					if( $(this).has(event.target).length ) return;
					if( $(this).parent().has(event.target).length ) return;
					if( $(this).hasClass('outside-none') ) return;
					$(this).parent().removeClass('toggle-visible').find('.toggle-content').removeClass('visible');
				});
			}
		});

		// Close button in toggle
	    var $closeTab = $('.toggle-content .btn-close');
	    $closeTab.on('click', function() {
	        $(this).closest('.toggle-visible').removeClass('toggle-visible').find('.dropdown-switcher').removeClass('visible');
	    });

		// add click map

		var $toggleTabMap  = $('.onclick-map');
		$toggleTabMap.on('click', function(){
			$(this).parent().toggleClass('toggle-visible').find('.toggle-content').toggleClass('visible');
		});
		var $closeMap = $('.onmap .btn-close');
		$closeMap.on('click', function() {
			$(this).closest('.tool-map').removeClass('toggle-visible').find('.toggle-content-close').removeClass('visible');
		});

		function _filterToggle(){
			var $body 			 = $('body');
            var filtersToggle    = $body.find('.sidebar-filters-toggle');
            if(!filtersToggle.length) return;
			var $layerednav 	 = $('#narrow-by-list, .filter-actions');
			var $layerednavBrand = $('#layerednav-filter-block-brand');
			if(!$body.hasClass('has-sidebar-filters-toggle') && ($layerednav.length || $layerednavBrand.length)){
				$body.addClass('has-sidebar-filters-toggle');
				$(document).on('click', '.sidebar-filters-toggle', function(event) {
					event.preventDefault();
					$body.toggleClass('filter-active');
					$('#layerednav-filter-block').toggleClass('active');
					$('#layerednav-filter-block-brand').toggleClass('active');
				});
			}
		}

	    function _increaseJnit(){
	    	var updateCart;
			$('.main').on("click", '.alo_qty_dec', function(){
			    var input = $(this).closest('.qty').find('input');
		        var value  = parseInt(input.val());
		        if(value) input.val(value-1);
		        clearTimeout(updateCart);
		        updateCart = setTimeout(function(){ $('.action.update').trigger('click'); }, 1000);
			});
		    $('.main').on("click", '.alo_qty_inc', function(){
		        var input = $(this).closest('.qty').find('input');
		        var value  = parseInt(input.val());
		        input.val(value+1);
		        clearTimeout(updateCart);
		        updateCart = setTimeout(function(){ $('.action.update').trigger('click'); }, 1000);
		    });

		    /* The code custom on product list */
	        $(document).on("click",  '.product-item .increase', function() {
	            var $input = $(this).closest('.product-item').find("input.qty");
	            $input.val(function(i, value) {
	                value = parseInt(value);
	                return value + 1;
	            });
	        });

	        $(document).on("click", '.product-item .reduced', function() {
	            var $input = $(this).closest('.product-item').find("input.qty");
	            var values = $input.val();
	            values = parseInt(values);
	            if (values > 1) {
	                $input.val(function(i, value) {
	                    value = parseInt(value);
	                    return value - 1;
	                });
	            }
	        });		    	
	    }

	    function _goTopJnit(){
			var $body = $('body'),
				$backtotop = $('#backtotop');
			if (!$backtotop.length) return;
			$backtotop.hide();
			var lastScrollTop = 0,
				documentHeight = Math.max(
				document.body.scrollHeight,
				document.documentElement.scrollHeight,
				document.body.offsetHeight,
				document.documentElement.offsetHeight,
				document.body.clientHeight,
				document.documentElement.clientHeight
			);
			window.addEventListener("scroll", (event) => {
				var status = window.scrollY;
				if (status + window.innerHeight >= documentHeight){
					$body.removeClass('scroll_down scroll_up scroll_init').addClass('scroll_down_end');
				} else if (status > lastScrollTop){
					$body.removeClass('scroll_up scroll_init scroll_down_end').addClass('scroll_down');
				} else if(status == lastScrollTop){
					$body.removeClass('scroll_down scroll_up scroll_down_end').addClass('scroll_init');
				} else {
					$body.removeClass('scroll_down scroll_init scroll_down_end').addClass('scroll_up');
				}
				lastScrollTop = status;
				if($('body').hasClass('cms-no-route')) return;
				if (status > documentHeight/10) $backtotop.fadeIn();
				else $backtotop.fadeOut();
			});
			$backtotop.on('click', function () {
				$('body,html').animate({scrollTop: 0}, 800);
				return false;
			});
	    }

		function _elevatorJnit(){
			/* elevator click*/ 
			var $megashop = $('.megashop');
			var length = $megashop.length;
			$megashop.each(function(index, el) {
				var elevator = $(this).find('.floor-elevator');
				elevator.attr('id', 'elevator-' +index);
				var bntUp 	= elevator.find('.btn-elevator.up');
				var bntDown = elevator.find('.btn-elevator.down');
				bntUp.attr('href', '#elevator-' + (index-1));
				bntDown.attr('href', '#elevator-' +(index+1));
				if(!index) bntUp.addClass('disabled');
				if(index == length-1) bntDown.addClass('disabled');
				elevator.find('.btn-elevator').on('click', function(e) {
					e.preventDefault();
					var target = this.hash;
					if(!$(document).find(target).length) return false;
					var $target = $(target);
					$('html, body').stop().animate({'scrollTop': $target.offset().top-50 }, 500);
					return false;
				});
			});
		}

	    // add equalheight category
		(function( $ ) {

			$.fn.equalHeights = function( options ) {
				var defaults = {
					onResize: 	true,
					onLoad: 	true
				};
				var settings = $.extend( {}, defaults, options );
				
				var topPositions = {},
					foundPosition = 0,
					$el = [],
					curHeight = 0,
					topPosition = 0,
					resizeTimer,
					$elements = $(this);
			 
				if ($elements.length < 2) return this;
				
				if ( settings.onResize ) {
					$(window).on('resize' , function(){
						if ( resizeTimer ) window.clearTimeout(resizeTimer);
						resizeTimer = window.setTimeout(function() {
							$elements.equalHeights( { onResize: false, onLoad: false } );
						}, 100);
					});
				};

				if ( settings.onLoad ) {
					$(window).on('load', function () {
						$elements.equalHeights( { onResize: false, onLoad: false } );
					});
				}
			 
				$elements.each(function() {
					$el = $(this);
					$el.height('auto');// restore original height from possible previous equalHeights()
					curHeight = $el.height();
			 
					if ( curHeight > 0 ) {
						topPosition = $el.position().top;
						foundPosition = topPosition in topPositions;
							 
						if(!foundPosition) {
							// First at this position, only store and set height
							topPositions[topPosition] = curHeight;
							$el.height(topPositions[topPosition]);
						} else {
							if(curHeight > topPositions[topPosition]) {
								// Tallest so far for this position, remember tallest and stretch others on this position
								topPositions[topPosition] = curHeight;
								$($elements).filter(function() {
									return $(this).position().top == topPosition;
								}).height(curHeight);
							} else {
								// Same or less high, maximize this one
								$el.height(topPositions[topPosition]);
							}
						}
					}
				});
			 
				return this;
			 
			};

		}( $ ));

	    // add equalheight category
		function _equalHeightJnit(selector){
			if(selector === undefined) selector = '.category-products.products-grid .items';
		    $(selector).each(function(){
		        var $this = $(this),
		            target = $this.find('>.item');
		        $this.find(target).equalHeights();
		    });
		}

		function _qsJnit(){
            var obj = arguments[0],
            	body = $('body');
            if(!$('#modals_quickview').length) body.append('<div id="modals_quickview" style="display:none"></div>');
            var _qsModal = $('#modals_quickview'),
            	quickajax= function(url){
                $.ajax({
                    url:url,
                    type:'POST',
                    showLoader: true,
                    cache:false,   
                    success:function(data){
                    	var typeProduct = data.match(/catalog_product_view_type\w*/g);
						typeProduct = typeProduct.length ? typeProduct[0].replace('catalog_product_view', 'quickview') : '';
						data = data.replace("[data-role=priceBox][data-price-box=product-id-", ".product-view [data-role=priceBox][data-price-box=product-id-");
                    	var pricebox = $('.price-box');
                    	pricebox.addClass('price-box-conflict-quickview');
                    	pricebox.removeClass('price-box');
                        _qsModal.html('<div class="content-quickview">' + data + '</div>');
                        if(!body.hasClass('open-quickview')){
                            body.addClass(`open-quickview ${typeProduct}`);
                            var modalsQuickview = body.find('.modals-quickview');
                            if(!modalsQuickview.length){
                                modal({
                                    type: 'popup',
                                    modalClass: 'modals-quickview',
                                    responsive: true,
                                    innerScroll: true,
                                    buttons: false,
                                    closed: function(){
                                        body.removeClass(`open-quickview ${typeProduct}`);
										var pricebox = $('.price-box-conflict-quickview');    
								        pricebox.addClass('price-box');
								        pricebox.removeClass('price-box-conflict-quickview'); 
                                    } 
                                }, _qsModal);
                            }
                            _qsModal.modal('openModal');
                        }
                        _qsModal.trigger('contentUpdated');                          
                    }
                });
                _qsModal.on('fotorama:load', function(){
                	var imgBox = _qsModal.find(".product-img-box");
                	if(imgBox.height() > imgBox.width()) _qsModal.find(".product-view .product-info-main.product-shop").height(imgBox.height());
                });
            }
            if(obj.url){
                quickajax(obj.url)
            } else {
                $(document).on('click', obj.itemClass, function(e) {
                    e.preventDefault();
                    quickajax($(this).data('url'))
                });
            }
		}

		_filterToggle()
		_increaseJnit()

		_goTopJnit()

		_elevatorJnit()

		$(window).on('resize contentUpdated swatch.initialized', function() {
			_equalHeightJnit();
		})

		_qsJnit({url : '',itemClass : '.quickview.autoplay'});

		$.fn.quickview 	 = _qsJnit;
		$.fn.equalheight = _equalHeightJnit;

		$('.bg-parallax').each(function(){$(this).parallax("50%",0.1);})

		$('.delivery-return .delivery-return-text').on('click', function () {
			modal({
				type: 'popup',
				modalClass: 'modals-deliveryguide',
				responsive: true, 
				autoOpen: true,
				buttons: false           
			}, '#delivery-return-popup');
		});

		$(document).on('click', '.crisp-chat-popup', function(e) {
			e.preventDefault();
			if (typeof $crisp !== 'undefined') {
				$crisp.push(['do', 'chat:open']);
			}
		});
	    $('body').one('mousemove', function() { 
			$('.suggest-slider').each(function() {
				if($(this).hasClass('autoplay')){
					var config = $(this).data();
					$(this).notifySlider(config);
				}
			});
		} );
		/* Fix error click my acount in checkout page of theme */
		$(document).on('click', '.proceed-to-checkout', function(e) {
			var authentication = $('[data-trigger="authentication"]');
			if(authentication.length){
				authentication.trigger('click');
			}else{
				var popupLogin = $("#authenticationPopup");
				if(popupLogin.length && popupLogin.find('input[name="username"]').length){
					modal({
						'type': 'popup',
						'modalClass': 'alo-modal-popup popup-authentication',
						'focus': '[name=username]',
						'responsive': true,
						'innerScroll': true,
						'autoOpen': true,
						'buttons': false
					}, popupLogin);
				}else {
					var popupLogin = $("#customer-popup-login");
					if(popupLogin.length && popupLogin.find('input[name="username"]').length){
						modal({
							'type': 'popup',
							'modalClass': 'modal-customer-login',
							'focus': '[name=username]',
							'responsive': true,
							'innerScroll': true,
							'autoOpen': true,
							'buttons': false
						}, popupLogin);
					}
				}
			}
		});

		/* Demo column */
		const colNumer = new URLSearchParams(window.location.search).get('col'),
			colRegex = /col-(\d+)|col-md-(\d+)|col-lg-(\d+)/g;
		if (document.querySelector('body.catalog-category-view, body.catalogsearch-result-index') && colNumer){
			const gridMode = document.querySelector(`.grid-view-mode .grid-mode.grid-${colNumer}`);
			if (gridMode) gridMode.click()
		} else if (document.querySelector('body.blog-category-view, body.blog-index-index') && colNumer) {
			$('.post-list .post-item').attr('class', function (index, value) {
				return value.replace(colRegex, (match, p1, p2, p3) => {
					return match.replace(/\d+/, Math.round(12 / colNumer));
				});
			});
		}

	});

});