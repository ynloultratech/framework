/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
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