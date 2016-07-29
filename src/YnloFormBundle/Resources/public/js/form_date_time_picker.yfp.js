/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

YnloFramework.FormDateTimePicker = {
    init: function () {
        var initialize = function () {
            $('[date-picker]').each(function () {
                var picker = $(this);

                //avoid double injection
                if (picker.attr('date-picker-initialized') !== undefined) {
                    return;
                } else {
                    picker.attr('date-picker-initialized', true);
                }

                //initialize
                if ($(this).attr('date-picker-style') == 'bootstrap') {
                    picker = $(picker).closest('div');
                }
                $(picker).datetimepicker($.parseJSON($(this).attr('date-picker-options')));
            })
        };
        initialize();
        $(document).on('ajaxSuccess', initialize);
    }
};