/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

YnloFramework.AdminListDetails = {
    config: {
        iconDown: 'fa fa-chevron-down',
        iconUp: 'fa fa-chevron-up'
    },
    init: function () {
        YnloFramework.AdminListDetails.initToggleListener();
    },
    initToggleListener: function (action) {
        var iconDown = YnloFramework.AdminListDetails.config.iconDown;
        var iconUp = YnloFramework.AdminListDetails.config.iconUp;

        $(document).on('show.bs.collapse', '[data-list-details]', function () {
            var id = $(this).data('list-details');
            var toggle = $('[data-list-details-toggle="' + id + '"]');
            toggle.addClass('active').find('i').removeClass(iconDown).addClass(iconUp);
            var details = $('#details_' + id);

            var url = toggle.data('list-details-url');
            if (details.find('.loader').length && url) {
                details.load(url);
            }
        });
        $(document).on('hidden.bs.collapse', '[data-list-details]', function () {
            var id = $(this).data('list-details');
            $('[data-list-details-toggle="' + id + '"]').removeClass('active').find('i').removeClass(iconUp).addClass(iconDown);
        });
    },
};