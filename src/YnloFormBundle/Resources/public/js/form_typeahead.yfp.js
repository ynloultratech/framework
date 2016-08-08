/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

YnloFramework.FormTypeahead = {
    init: function () {
        var initialize = function () {
            $('[typeahead]').each(function () {
                var element = $(this);
                //avoid double injection
                if (element.attr('typeahead-initialized') !== undefined) {
                    return;
                } else {
                    element.attr('typeahead-initialized', true);
                }
                if (element.attr('typeahead-source') != undefined) {
                    $(element).typeahead({source: $.parseJSON(element.attr('typeahead-source'))});
                } else {
                    $(element).typeahead({
                        source: function (query, process) {
                            return $.get(element.attr('autocomplete-url') + '&q=' + query, function (data) {
                                return process(data);
                            });
                        }
                    });
                }
            })
        };
        initialize();
        $(document).on('ajaxSuccess', initialize);
    }
};