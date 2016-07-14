/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

YnloFramework.FormToggle = {
    init: function () {
        var initialize = function () {
            $('[form-toggle]').each(function () {
                //avoid double injection
                if ($(this).attr('form-toggle-initialized') !== undefined) {
                    return;
                } else {
                    $(this).attr('form-toggle-initialized', true);
                }
                $(this).formToggle($.parseJSON($(this).attr('form-toggle-options')));
            })
        };
        initialize();
        $(document).on('ajaxSuccess', initialize);
    }
};