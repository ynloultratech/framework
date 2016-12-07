/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

YnloFramework.Debugger = {
    lastError: null,
    lastDebugTokenLink: null,
    init: function () {
        if (!YnloFramework.debug) {
            return;
        }
        $(document).on('ajaxStart pjax:start', function (event, xhr) {
            if ($('.ynlo-debugger-error').length) {
                $('.ynlo-debugger-error').remove();
            }
        });

        $(document).on('pjax:error', function (event, xhr) {
            YnloFramework.Debugger.showAjaxError(xhr, true);
        });
        $(document).on('ajaxError', function (event, xhr) {
            YnloFramework.Debugger.showAjaxError(xhr, false);
        });

        //keep syToolbar up to date when pjax target is not the body
        $(document).on('ajaxSuccess', function (event, output, status, xhr) {
            if (typeof xhr.getResponseHeader === 'function') {
                if (xhr.getResponseHeader('X-MODAL')) {
                    YnloFramework.Debugger.updateProfiler(xhr);
                }
            }
        });

        $(document).on('pjax:success', function (event, output, status, xhr) {
            if (YnloFramework.Pjax.config.target !== 'body') {
                YnloFramework.Debugger.updateProfiler(xhr);
            }
        });
    },
    updateProfiler: function (xhr) {
        if (xhr.getResponseHeader('X-Debug-Token')) {
            //TODO: resolve the current environment
            var url = '/app_dev.php/_wdt/' + xhr.getResponseHeader('X-Debug-Token');
            if ($('body').find('.sf-toolbarreset').length) {
                $('body').find('.sf-toolbarreset > .sf-toolbar-block').addClass('sf-ajax-request-loading');
                $.ajax(url, {
                    success: function (response) {
                        response = $('<div>' + response + '</div>');
                        $('body').find('.sf-toolbarreset > .sf-toolbar-block').remove();
                        $('body').find('.sf-toolbarreset').append(response.find('.sf-toolbarreset > .sf-toolbar-block'));
                        $('.sf-toolbar-info').css('right', '0');
                    }
                });
            } else {
                $('<div class="profiler"></div>').load(url).appendTo('body');
            }
        }
    },
    showAjaxError: function (xhr, expanded) {
        if (xhr.statusText == 'abort' || xhr.statusText == 'canceled' || xhr.status == 403 || xhr === YnloFramework.Debugger.lastError) {
            return;
        }

        if ($('.ynlo-debugger-error').length) {
            $('.ynlo-debugger-error').remove();
        }

        var audio = new Audio('/bundles/ynloframework/media/error.mp3');
        audio.play();

        var element = $('<div class="ynlo-debugger-error"></div>');
        $('body').append(element);
        element.html('(' + xhr.status + ') ' + xhr.statusText);
        element.show();
        element.append('<a data-pjax="false" href="#" onclick="YnloFramework.Debugger.showLastError()" style="margin: 0 5px">[RESPONSE]</a>');

        YnloFramework.error('Error Code: ' + xhr.status + ', Error: ' + xhr.statusText);

        YnloFramework.Debugger.lastError = xhr;
        if (xhr.getResponseHeader('X-Debug-Token-Link')) {
            YnloFramework.Debugger.lastDebugTokenLink = xhr.getResponseHeader('X-Debug-Token-Link');
            element.append('<a data-pjax="false" href="#" onclick="YnloFramework.Debugger.showLastError(true)" style="margin: 0 5px">[PROFILE]</a>');
        }

        element.append('<a data-pjax="false" href="#" onclick="YnloFramework.Debugger.close()">[X]</a>');

        if (expanded) {
            YnloFramework.Debugger.showLastError();
        }
    },
    showLastError: function (profile) {
        if ($('.ynlo-debugger-error-preview').length) {
            $('.ynlo-debugger-error-preview').remove();
        }
        var element = $('<div class="ynlo-debugger-error-preview"><a href="#" data-pjax="false" onclick="$(\'.ynlo-debugger-error-preview\').remove();">[CLOSE]</a><iframe frameborder="0"></iframe></div>');

        if (profile == undefined && YnloFramework.Debugger.lastDebugTokenLink) {
            element.find('a').after('<a data-pjax="false" style="margin: 0 5px 0 0" href="#" onclick="YnloFramework.Debugger.showLastError(true)">[PROFILE]</a> ');
        }
        $('body').append(element);
        var iframe = element.find('iframe');
        if (profile) {
            iframe.prop('src', YnloFramework.Debugger.lastDebugTokenLink + '?panel=exception');
        } else {
            iframe.prop('srcdoc', YnloFramework.Debugger.lastError.responseText);
        }
        element.show();
    },
    close: function () {
        $('.ynlo-debugger-error').remove();
    }
};
