/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

YnloFramework.FormColorPicker = {
    init: function () {
        var initialize = function () {
            $('[color-picker]').each(function () {
                var picker = $(this);

                //avoid double injection
                if (picker.attr('color-picker-initialized') !== undefined) {
                    return;
                } else {
                    picker.attr('color-picker-initialized', true);
                }
                $(picker).spectrum($.parseJSON($(this).attr('color-picker-options')));
            })
        };
        initialize();
        $(document).on('ajaxSuccess', initialize);
    }
};