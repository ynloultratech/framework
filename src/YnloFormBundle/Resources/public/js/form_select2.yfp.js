/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

YnloFramework.FormSelect2 = {
    init: function () {
        var initialize = function () {
            $('[select2]').each(function () {
                //avoid double injection
                if ($(this).attr('select2-initialized') !== undefined) {
                    return;
                } else {
                    $(this).attr('select2-initialized', true);
                }
                var options = $.parseJSON($(this).attr('select2-options'));
                var element = $(this);

                //hack to allow search inside template result attribute
                var matchTemplate = function (term, text, data) {
                    text = $(data.element).data('template-result');
                    if (text != undefined && text.toUpperCase().indexOf(term.toUpperCase()) >= 0) {
                        return true;
                    }

                    return false;
                };
                $.fn.select2.amd.require(['select2/compat/matcher'], function (oldMatcher) {

                    var defaults = {
                        matcher: oldMatcher(matchTemplate),
                        templateResult: function (result) {
                            if (result.element && $(result.element).data('template-result') && $(result.element).data('template-result') != undefined) {
                                return $(result.element).data('template-result');
                            }

                            return result.text;
                        },
                        templateSelection: function (result) {
                            if (result.element && $(result.element).data('template-selection') && $(result.element).data('template-selection') != undefined) {
                                return $(result.element).data('template-selection');
                            }

                            if (result.selection_text) {
                                return result.selection_text;
                            }

                            return result.text;
                        },
                        escapeMarkup: function (markup) {
                            return markup;
                        }
                    };
                    options = $.extend({}, defaults, options);

                    if ($(element).attr('autocomplete-url')) {
                        options.ajax = {
                            url: $(element).attr('autocomplete-url'),
                            dataType: 'json',
                            delay: 300,
                            data: function (params) {
                                return {
                                    q: params.term, // search term
                                    page: params.page
                                };
                            }
                        };
                    }

                    $(element).select2(options);

                    //small hack to allow search inside bootstrap modals
                    //issue: http://stackoverflow.com/questions/18487056/select2-doesnt-work-when-embedded-in-a-bootstrap-modal/18487440#18487440
                    $(element).on('select2:open', function () {
                        if ($(this).parents('.modal').length) {
                            $(this).parents('.modal').removeAttr('tabindex');
                        }
                    });
                });
            })
        };
        initialize();
        $(document).on('ajaxSuccess', initialize);
    }
};