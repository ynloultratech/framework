/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function($){
    var timeOut = 400;
    $.fn.onVisible = function (callback) {
        return $(this).each(function () {
            var element = $(this);

            //avoid doble injection
            if (element.data('_onvisible') == true) {
                return;
            } else {
                element.data('_onvisible', true);
            }

            var verify = function () {
                if (element.visible()) {
                    if (element.data('_visible-status') == 'hidden') {
                        //fire event
                        $(element).each(callback);
                    }

                    element.data('_visible-status', 'visible');
                    setTimeout(verify, timeOut);

                } else {
                    element.data('_visible-status', 'hidden');
                    setTimeout(verify, timeOut);
                }
            };

            verify();
        });
    }
})(jQuery);

/**
 * Copyright 2012, Digital Fusion
 * Licensed under the MIT license.
 * http://teamdf.com/jquery-plugins/license/
 *
 * @author Sam Sehnert
 * @desc A small plugin that checks whether elements are within
 *       the user visible viewport of a web browser.
 *       only accounts for vertical position, not horizontal.
 */
!function (t) {
    var i = t(window);
    t.fn.visible = function (t, e, o) {
        if (!(this.length < 1)) {
            var r = this.length > 1 ? this.eq(0) : this, n = r.get(0), f = i.width(), h = i.height(), o = o ? o : "both", l = e === !0 ? n.offsetWidth * n.offsetHeight : !0;
            if ("function" == typeof n.getBoundingClientRect) {
                var g = n.getBoundingClientRect(), u = g.top >= 0 && g.top < h, s = g.bottom > 0 && g.bottom <= h, c = g.left >= 0 && g.left < f, a = g.right > 0 && g.right <= f, v = t ? u || s : u && s, b = t ? c || a : c && a;
                if ("both" === o)return l && v && b;
                if ("vertical" === o)return l && v;
                if ("horizontal" === o)return l && b
            } else {
                var d = i.scrollTop(), p = d + h, w = i.scrollLeft(), m = w + f, y = r.offset(), z = y.top, B = z + r.height(), C = y.left, R = C + r.width(), j = t === !0 ? B : z, q = t === !0 ? z : B, H = t === !0 ? R : C, L = t === !0 ? C : R;
                if ("both" === o)return !!l && p >= q && j >= d && m >= L && H >= w;
                if ("vertical" === o)return !!l && p >= q && j >= d;
                if ("horizontal" === o)return !!l && m >= L && H >= w
            }
        }
    }
}(jQuery);
