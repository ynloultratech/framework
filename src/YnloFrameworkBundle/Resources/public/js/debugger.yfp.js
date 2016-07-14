/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

YnloFramework.Debugger = {
    lastAjaxRequest: null,
    lastDebugTokenLink: null,
    init: function () {
        if (!YnloFramework.debug) {
            return;
        }
        $(document).on('ajaxStart', function (event, xhr) {
            if ($('.ynlo-debugger-error').length) {
                $('.ynlo-debugger-error').remove();
            }
        });

        $(document).on('ajaxError', function (event, xhr) {
            if (xhr.statusText == 'abort' || xhr.statusText == 'canceled' || xhr.status == 403) {
                return;
            }

            if ($('.ynlo-debugger-error').length) {
                $('.ynlo-debugger-error').remove();
            }
            var element = $('<div class="ynlo-debugger-error"></div>');
            $('body').append(element);
            element.html('(' + xhr.status + ') ' + xhr.statusText);
            element.show();
            element.append('<a data-pjax="false" href="#" onclick="YnloFramework.Debugger.showLastRequest()" style="margin: 0 5px">[RESPONSE]</a>');

            YnloFramework.error('Error Code: ' + xhr.status + ', Error: ' + xhr.statusText);

            YnloFramework.Debugger.lastAjaxRequest = xhr;
            if (xhr.getResponseHeader('X-Debug-Token-Link')) {
                YnloFramework.Debugger.lastDebugTokenLink = xhr.getResponseHeader('X-Debug-Token-Link');
                element.append('<a data-pjax="false" href="#" onclick="YnloFramework.Debugger.showLastRequest(true)">[PROFILE]</a>');
            }
        });
    },
    showLastRequest: function (profile) {
        if ($('.ynlo-debugger-error-preview').length) {
            $('.ynlo-debugger-error-preview').remove();
        }
        var element = $('<div class="ynlo-debugger-error-preview"><a href="#" data-pjax="false" onclick="$(\'.ynlo-debugger-error-preview\').remove();">[ CLOSE ]</a><iframe frameborder="0"></iframe></div>');
        $('body').append(element);
        var iframe = element.find('iframe');
        if (profile) {
            iframe.prop('src', YnloFramework.Debugger.lastDebugTokenLink + '?panel=exception');
        } else {
            iframe.prop('srcdoc', YnloFramework.Debugger.lastAjaxRequest.responseText);
        }
        element.show();
    }
};