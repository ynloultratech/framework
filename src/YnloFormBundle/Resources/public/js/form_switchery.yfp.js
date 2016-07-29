/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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