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
                var $element = $(this);

                if ($element.data('switchery')) {
                    return;
                }

                $element.data('switchery', Switchery($element[0], $.parseJSON($element.attr('switchery-options'))));

                //require(['switchery'], function (switchery) {
                    //avoid double injection
                    //if ($(element).attr('switchery-initialized') !== undefined) {
                    //    return;
                    //} else {
                    //    $(element).attr('switchery-initialized', true);
                    //}
                    //FIXME: Switchery are not working with RequireJs, fix or migrate to bootstrap switch
                  //  var e =  Switchery(element, $.parseJSON($(element).attr('switchery-options')));
                //})
            });
        };
        initialize();
        $(document).on('ajaxSuccess', initialize);
    }
};
