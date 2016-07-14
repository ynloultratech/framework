/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

YnloFramework.FormTypeahead = {
    init: function () {
        var initialize = function () {
            $('[typeahead]').each(function () {
                //avoid double injection
                if ($(this).attr('typeahead-initialized') !== undefined) {
                    return;
                } else {
                    $(this).attr('typeahead-initialized', true);
                }
                var options = {
                    datumTokenizer: Bloodhound.tokenizers.whitespace,
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                };
                if ($(this).attr('typeahead-source') != undefined) {
                    options.local = $.parseJSON($(this).attr('typeahead-source'))
                } else {
                    options.remote = {
                        url: $(this).attr('autocomplete-url') + '&q=%QUERY',
                        wildcard: '%QUERY'
                    }
                }
                var engine = new Bloodhound(options);

                $(this).typeahead({
                    minLength: 1
                }, {
                    source: engine
                });
            })
        };
        initialize();
        $(document).on('ajaxSuccess', initialize);
    }
};