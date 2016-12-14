/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

YnloFramework.Notify = {
    stack_center: {"dir1": "down", "dir2": "right", "firstpos1": 25, "firstpos2": 0},
    init: function () {
        YnloFramework.Notify.configureFirstPos2();
        $(window).resize(YnloFramework.Notify.configureFirstPos2);
    },
    flash: function (type, message, options) {
        var defaults = {
            text: message,
            width: '500px',
            animation: 'fade',
            animate_speed: 'fast',
            delay: 6000,
            stack: YnloFramework.Notify.stack_center,
            buttons: {
                sticker: false
            }
        };

        switch (type) {
            case 'success':
                defaults['addclass'] = 'alert-styled-left bg-success';
                defaults['type'] = 'success';
                break;
            case 'danger':
                defaults['addclass'] = 'alert-styled-left alert-styled-danger bg-danger';
                defaults['type'] = 'custom';
                break;
            case 'warning':
                defaults['addclass'] = 'alert-styled-left alert-styled-warning bg-warning';
                defaults['type'] = 'custom';
                break;
            case 'info':
                defaults['addclass'] = 'alert-styled-left bg-info';
                defaults['type'] = 'info';
                break;
        }

        require(['pnotify'], function (pnotify) {
            new pnotify($.extend(defaults, options));
        });
    },
    configureFirstPos2: function () {
        require(['pnotify'], function (pnotify) {
            YnloFramework.Notify.stack_center.firstpos2 = YnloFramework.Notify.getFirstPos2(pnotify);
        });
    },
    getFirstPos2: function (pnotify) {
        return ($(window).width() / 2) - (Number(pnotify.prototype.options.width.replace(/\D/g, '')) / 2);
    }
};
