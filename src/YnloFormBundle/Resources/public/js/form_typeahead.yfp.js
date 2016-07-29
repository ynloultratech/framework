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