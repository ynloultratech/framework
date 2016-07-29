/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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