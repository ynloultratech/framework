/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
                $(this).formToggle();
                //allow reverse in the same input
                $(this).formToggle({
                    dataAttribute: 'reverse-toggle',
                    reverse: true
                });
            })
        };
        initialize();
        $(document).on('ajaxSuccess', initialize);
    }
};