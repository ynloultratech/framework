/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

YnloFramework.FormSwitchery = {
    init: function () {
        var initialize = function () {
            $('[switchery]').each(function () {
                //avoid double injection
                if ($(this).attr('switchery-initialized') !== undefined) {
                    return;
                } else {
                    $(this).attr('switchery-initialized', true);
                }
                var e = new Switchery(this, $.parseJSON($(this).attr('switchery-options')));
            })
        };
        initialize();
        $(document).on('ajaxSuccess', initialize);
    }
};