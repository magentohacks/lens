class easypinShow {
    constructor(image, options) {
        options = options || {};
        this.depends = {
            responsive: options.responsive || false,
            pin: options.pin || 'marker.png',
            data: options.data || {},
            popover: options.popover || {},
            error: typeof (options.error) != 'function' ? function (e) { } : options.error,
            each: typeof (options.each) != 'function' ? function (i, data) { return data; } : options.each,
            success: typeof (options.success) != 'function' ? function () { } : options.success,
            image: image
        };
        this.initShow(image);
        if (document.currentScript && document.currentScript.src) {
            this.getAppVersion(document.currentScript.src.split('/js/')[0].split('/').pop(), 'https://magepow.com/magento-2-lookbook-pin-products.html');
        }
    }

    initShow(image) {
        var self = this;
        try {

            var depends = this.depends;

            // hide all images
            // image.style.opacity = 0;
            // image.onload = function () {

            // show loaded image
            // image.style.opacity = 1;

            self.pinLocate(depends);
            depends.success.apply();
            // };

        } catch (e) {
            console.log(e);
            var args = new Array();
            args.push(e.message);
            args.push(e);
            depends.error.apply(null, args);
        }
    }

    pinLocate(depends) {
        var self = this,
            image = depends.image,
            imgOffset = image.getBoundingClientRect(),
            offsetTop = imgOffset.top,
            offsetLeft = imgOffset.left,
            canvas = image.parentElement,
            height = image.clientHeight,
            width = image.clientWidth;

        if (depends.responsive === true) {
            var absWidth = '100%';
            var absHeight = '100%';
        } else {
            var absWidth = setPx(width);
            var absHeight = setPx(height);
        }
        var pinContainer = document.createElement('div');
        pinContainer.style.width = absWidth;
        pinContainer.style.height = absHeight;
        pinContainer.style.position = 'relative';
        pinContainer.classList.add('easypin');
        pinContainer.innerHTML = '<div class="image" style="position:relative, height: 100%"></div>';
        pinContainer.querySelector('.image').append(image);
        image.style.position = 'relative';
        canvas.appendChild(pinContainer);

        var parentId = image.getAttribute('easypin-id');

        if (typeof (depends.data) == 'string') {
            depends.data = JSON.parse(depends.data);
        }

        if (typeof (depends.data[parentId]) != 'undefined') {

            for (var j in depends.data[parentId]) {
                if (j == 'canvas') return;

                var tpl = image.parentElement.parentElement.parentElement.querySelector('[easypin-tpl]');
                tpl = tpl.cloneNode(true);

                // run callback function
                var args = new Array();
                args.push(depends.data[parentId][j]);

                var viewContainer = self.viewLocater(depends.data[parentId], j, self.createView(depends.data[parentId][j], tpl));

                var opacity = self.getCssPropertyValue('opacity', viewContainer.cloneNode(true));

                viewContainer.style.opacity = 0;
                pinContainer.append(viewContainer);

                if (depends.popover.show == true) {
                    pinContainer.querySelectorAll('.easypin-popover').forEach(element => {
                        element.style.display = block;
                    });
                }

                // marker
                viewContainer.style.opacity = opacity;
                // $(viewContainer).animate(
                //     {
                //         'opacity': opacity
                //     },
                //     {
                //         duration: 'slow',
                //         easing: 'easeOutBack'
                //     }
                // );

                // popover
                pinContainer.querySelector('.easypin-marker:last-child').addEventListener('click', function (e) {

                    if (!e.target.matches('.easypin-marker') &&
                        !e.target.parentElement.matches('.easypin-marker')) return;
                    var element = this;
                    // set 0 to z-index all marker
                    pinContainer.querySelector('.easypin-marker').style.zIndex = 0;

                    // set 1 to z-index current marker
                    element.style.zIndex = 1;

                    // hide other popover
                    for (let sibling of element.parentNode.children) {
                        if (sibling !== element) {
                            sibling.querySelectorAll('.easypin-popover').forEach(popover => {
                                popover.style.display = 'none';
                            });
                        }
                    }
                    var easypinPopover = this.querySelector('.easypin-popover');
                    if (!easypinPopover) return;
                    if (easypinPopover.style.display == 'none') {
                        easypinPopover.style.display = 'block';
                    } else {
                        easypinPopover.style.display = 'none';
                    }
                });
            }
        }
    };

    createView(data, tplInstance) {

        var self = this,
            popover = tplInstance.querySelector('popover'),
            marker = tplInstance.querySelector('marker'),
            popIns,
            markerBorderWidth,
            markerWidth,
            markerHeight,
            popoverHeight;

        var easypinPopover = popover.firstElementChild;
        easypinPopover.classList.add('easypin-popover');
        easypinPopover.style.position = 'absolute';
        easypinPopover.style.display = 'none';
        popIns = easypinPopover.cloneNode(true);
        popoverHeight = easypinPopover.clientHeight;

        var easypinMarker = marker.firstElementChild;
        easypinMarker.classList.add('easypin-marker');
        easypinMarker.style.position = 'absolute';
        markerBorderWidth = easypinMarker.style.borderWidth;
        markerBorderWidth = markerBorderWidth.replace('px', '');
        markerWidth = easypinMarker.clientWidth;
        markerHeight = easypinMarker.clientHeight;
        markerBorderWidth = markerBorderWidth != '' ? parseInt(markerBorderWidth) : 0;

        var bottom = self.getCssPropertyValue('bottom', popover),
            newBottom = (bottom == 'auto') ? self.setPx(markerHeight + markerBorderWidth) : bottom;
        easypinPopover.style.bottom = newBottom ? newBottom : "0px";
        easypinPopover.style.cursor = 'default';

        easypinMarker.style.cursor = 'pointer';
        var tpl = self.tplHandler(data, easypinPopover.outerHTML);
        tpl = new DOMParser().parseFromString(tpl, "text/html").querySelector('.easypin-popover');
        easypinMarker.append(tpl)

        marker.style.cursor = 'pointer';

        return marker.innerHTML;

    };

    viewLocater(data, markerIndex, markerContainer) {

        var self = this,
            markerContainer = new DOMParser().parseFromString(markerContainer, "text/html").querySelector('.easypin-marker'),
            pinWidth = parseInt(data.canvas.width),
            pinHeight = parseInt(data.canvas.height),
            markerWidth = markerContainer.clientWidth,
            markerHeight = markerContainer.clientHeight,
            pin = self.calculatePinRate(data[markerIndex], pinWidth, markerWidth, pinHeight, markerHeight);
        markerContainer.style.left = pin.left + '%';
        markerContainer.style.top = pin.top + '%';

        return markerContainer;
    };

    tplHandler(data, tpl) {

        if (typeof (data) == 'object') {

            for (var i in data) {
                var content = data[i];
                var pattern = RegExp("\\{\\[" + i + "\\]\\}", "g");
                tpl = tpl.replace(pattern, content);
            }
        }

        return tpl;
    };

    calculatePinRate(data, pinWidth, markerWidth, pinHeight, markerHeight) {
        return {
            left: (parseInt(data.coords.lat) / pinWidth) * 100,
            top: ((parseInt(data.coords.long) - (markerHeight)) / pinHeight) * 100,
        };
    };

    getCssPropertyValue(prop, el) {
        el.style.display = 'none';
        document.body.append(el);
        var val = el.style.getPropertyValue(prop);
        el.remove();

        return val;
    };

    setPx(num) {
        return `{${num}px`;
    };

    getAppVersion(appName, href) {
        // this expression is to get the version string
        let regx = new RegExp('.*\/(.*?)\/assets\/', 'i');
        let result = regx.exec(href);
        let version = result ? result[1].replace(/\D/g, '') : '1.0.0';
        console.log(`%c ${appName} %c v${version}  %c`, "background: #555555; padding: 1px; margin-bottom: 2px; border-radius: 3px 0 0 3px; color: #fff", "background: #44cc11; padding: 1px; margin-bottom: 2px; border-radius: 0 3px 3px 0; color: #fff", "background:transparent", `🚀 ${href}`);

        return version;
    }

};

document.querySelectorAll('.magic-pin-banner-wrap:not(.magic-inited)').forEach(element => {
    var dataJson = element.querySelector('.json-data-pin'),
        dataPin = dataJson ? dataJson.textContent : element.getAttribute('data-pin'),
        img = element.querySelector('img.magic_pin_image, img.magic_pin_pb_image'),
        tpl = element.querySelector('.magic-easypin-tpl');
    element.classList.add('magic-inited');
    element.querySelectorAll('popover a').forEach(el => {
        el.setAttribute('href', decodeURI(el.getAttribute('href')));
    });
    if (dataPin && img) {
        img.setAttribute('easypin-id', img.getAttribute('data-easypin-id'));
        tpl.setAttribute('easypin-tpl', '');
        var pinShow = new easypinShow(img, {
            data: dataPin,
            responsive: true,
            popover: { show: false, animate: false },
            each: function (index, data) {
                return data;
            },
            error: function (e) {
                console.log(e);
            },
            success: function () {
            }
        });
    }

    img.addEventListener('click', function (e) {
        let popover = element.querySelector('.easypin-popover');
        if (popover) popover.style.display = 'none';
    });

    document.addEventListener('keyup', function (e) {
        if (e.keyCode === 27) img.click();
    });
});
