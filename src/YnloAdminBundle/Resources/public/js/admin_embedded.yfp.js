/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

YnloFramework.AdminEmbedded = {
    init: function () {
        $('[admin-embedded]').each(function () {
            var $embedded = $(this);

            setTimeout(function () {
                // verify is visible for lazy load
                if ($embedded.visible()) {
                    YnloFramework.AdminEmbedded._load($embedded);
                } else {
                    $embedded.onVisible(function () {
                        YnloFramework.AdminEmbedded._load($embedded);
                    });
                }
            }, 200);
        });

        $(document).on('ajaxSuccess', YnloFramework.AdminEmbedded.init);
    },
    refresh: function (id) {
        var $embedded = $('[admin-embedded="' + id + '"]');

        if (0 === $embedded.length) {
            return; //not found
        }

        this._load($embedded, true);
    },
    _load: function ($embedded, force) {
        if (!$embedded.is(':empty') && !force) return;

        $embedded.trigger('ynlo_admin.embedded_admin.before_load');
        $embedded.load($embedded.attr('admin-embedded-url'), function () {
            $embedded.trigger('ynlo_admin.embedded_admin.after_load');
        });
    }
};
