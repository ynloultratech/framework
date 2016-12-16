/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//Location
YnloFramework.Location = {
    getURLParameter: function (param, url) {
        if (url == 'undefined') {
            url = window.location.hash;
        }
        return decodeURIComponent((new RegExp('[?|&]' + param + '=' + '([^&;]+?)(&|#|;|$)').exec(url) || [, ""])[1].replace(/\+/g, '%20')) || null;
    },
    updateQueryParam: function (key, value, update) {

        update = typeof update !== 'undefined' ? update : true;

        url = window.location.href;
        var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
            hash;

        if (re.test(url)) {
            if (typeof value !== 'undefined' && value !== null)
                url = url.replace(re, '$1' + key + "=" + value + '$2$3');
            else {
                hash = url.split('#');
                url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
                if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                    url += '#' + hash[1];
            }
        }
        else {
            if (typeof value !== 'undefined' && value !== null) {
                var separator = url.indexOf('?') !== -1 ? '&' : '?';
                hash = url.split('#');
                url = hash[0] + separator + key + '=' + value;
                if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                    url += '#' + hash[1];
            }
        }

        if (update) {
            YnloFramework.Location.go(url);
        } else {
            return url;
        }
    },
    refresh: function () {
        if (YnloFramework.hasPlugin('Pjax') && -1 === window.location.href.indexOf('data-pjax=0')) {
            YnloFramework.Pjax.refresh();
        } else {
            window.location = window.location;
        }
    },
    load: function (url) {
        if (YnloFramework.hasPlugin('Pjax') && -1 === url.indexOf('data-pjax=0')) {
            YnloFramework.Pjax.load(url);
        } else {
            window.location = url;
        }
    },
    go: function (url) {
        YnloFramework.Location.load(url);
    }
};
