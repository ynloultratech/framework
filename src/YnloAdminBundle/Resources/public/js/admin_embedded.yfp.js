/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

YnloFramework.AdminEmbedded = {
    init: function () {
        $('[admin-embedded]').each(function () {
            var $embedded = $(this),
                url = $embedded.attr('admin-embedded-url');

            // load remote content
            var loadEmbedded = function () {
                $embedded.trigger('ynlo_admin.embedded_admin.before_load');
                $embedded.load(url, function () {
                    $embedded.trigger('ynlo_admin.embedded_admin.after_load');
                });
            };

            var init = function () {
                $embedded.is(':empty') && loadEmbedded();
            };

            setTimeout(function () {
                // verify is visible for lazy load
                if ($embedded.visible()) {
                    init();
                } else {
                    $embedded.onVisible(init);
                }
            }, 200);
        });
    }
};
