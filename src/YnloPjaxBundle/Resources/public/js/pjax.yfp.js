/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

YnloFramework.Pjax = {
    config: {
        target: 'body',
        links: 'a:not([data-pjax="false"])a:not([href="#"])a:not([target="_blank"])',
        forms: 'form:not([data-pjax="false"])'
    },
    init: function () {

        if (this.config.links) {
            this._setupLinks();
        }
        if (this.config.forms) {
            this._setupForms();
        }

        YnloFramework.Pjax.pushState(window.location.href);

        $(window).off('popstate.pjax');
        $(window).on("popstate.pjax", function (event) {
            YnloFramework.Pjax.load(window.location.href);
        });
    },
    _setupLinks: function () {
        $(document).off('click.pjax');
        $(document).on('click.pjax', YnloFramework.Pjax.config.links , function (event) {
            var url = $(this).attr("href");

            if (!url) {
                return;
            }

            var pjaxLinkEvent = jQuery.Event("pjax:link");
            $(this).trigger(pjaxLinkEvent, [url]);
            if (pjaxLinkEvent.isPropagationStopped() || pjaxLinkEvent.isDefaultPrevented()) {
                event.preventDefault && event.preventDefault();
                return false;
            }

            //ignore external domains
            if (url.indexOf('://') > -1 && url.indexOf(document.domain) === -1) {
                return true;
            }

            event.preventDefault && event.preventDefault();

            YnloFramework.Pjax.load(url);

            return false;
        });
    },
    _setupForms: function () {
        $(document).off('submit.pjax');
        $(document).on('submit.pjax', YnloFramework.Pjax.config.forms, function (event) {
            var $form = $(this);

            event.preventDefault();

            var url = window.location.href;
            if ($form.attr('action')) {
                url = $form.attr('action');
            }

            $form.ajaxSubmit({
                beforeSend: function (xhr) {
                    YnloFramework.Pjax._xhr = xhr;
                    xhr.setRequestHeader("X-PJAX", 'true');
                    YnloFramework.Pjax.pushState(url);
                    $(document).trigger('pjax:beforeSend', [xhr]);
                },
                success: function (output, status, xhr) {
                    if (typeof xhr.getResponseHeader === 'function') {
                        if (xhr.getResponseHeader('X-PJAX-URL')) {
                            url = xhr.getResponseHeader('X-PJAX-URL');
                        }
                    }

                    YnloFramework.Pjax.pushResponse(url, output);
                    $(document).trigger('pjax:success', [output, status, xhr]);
                },
                error: function (xhr, reason) {
                    if (reason == 'abort') {
                        return;
                    }
                    YnloFramework.Pjax.pushResponse(url, xhr.responseText);
                    $(document).trigger('pjax:error', [xhr, reason]);
                }
            });
        })
    },
    _xhr: null,
    refresh: function () {
        YnloFramework.Pjax.load(window.location.href);
    },
    load: function (url) {
        if (YnloFramework.Pjax._xhr) {
            YnloFramework.Pjax._xhr.abort();
        }
        YnloFramework.Pjax._xhr = $.ajax({
            url: url,
            beforeSend: function (xhr) {
                xhr.setRequestHeader("X-PJAX", 'true');
                YnloFramework.Pjax.pushState(url);
                $(document).trigger('pjax:beforeSend', [xhr]);
            },
            success: function (output, status, xhr) {
                YnloFramework.Pjax.pushResponse(url, output);

                if (typeof xhr.getResponseHeader === 'function') {
                    if (xhr.getResponseHeader('X-PJAX-URL')) {
                        url = xhr.getResponseHeader('X-PJAX-URL');
                    }
                }

                $(document).trigger('pjax:success', [output, status, xhr]);
            },
            error: function (xhr, reason) {
                if (reason == 'abort') {
                    return;
                }
                YnloFramework.Pjax.pushResponse(url, xhr.responseText);
                $(document).trigger('pjax:error', [xhr, reason]);
            }
        });
    },
    pushState: function (url) {
        window.history.pushState({id: (new Date).getTime()}, '', url);
    },
    pushResponse: function (url, response) {

        if (!response) {
            return;
        }

        //replace the special `HTML` tags with prefixed tags
        //http://stackoverflow.com/questions/14423257/find-body-tag-in-an-ajax-html-response
        response = response.replace(/(<\/?)html( .+?)?>/gi, '$1html_escaped$2>', response);
        response = response.replace(/(<\/?)body( .+?)?>/gi, '$1body_escaped$2>', response);
        response = response.replace(/(<\/?)head( .+?)?>/gi, '$1head_escaped$2>', response);
        response = '<root>' + response + '</root>';
        var target = YnloFramework.Pjax.config.target;
        if (target == 'body') {
            target += '_escaped';
        }

        var innerResponse = response;
        if ($(response).find(target).length > 0) {
            innerResponse = $(response).find(target).html();

        }
        $(YnloFramework.Pjax.config.target).html(innerResponse);

        if ($(response).find('title').length > 0) {
            document.title = $(response).find('title').html();
        }

        //response out of the element, the layout
        //required to load scripts and styles
        var layout = '';
        if ($(response).find(target).length > 0) {
            var res = $(response);
            res.find(target).html('');
            layout = res.html();
        }

        //load scripts and styles files inside response head if have
        var head = $(response).find('head_escaped');
        if (head.length > 0) {

            YnloFramework.Pjax.loadScripts(head);
            YnloFramework.Pjax.loadStylesheets(head);
        }
    },
    loadScripts: function (output) {
        //already loaded scripts to avoid load again
        var loadedScripts = [];
        $(document).find('script').each(function () {
            if ($(this).attr('src')) {
                loadedScripts.push($(this).attr('src'));
            }
        });

        var onThisPage = [];
        $(output).find('script').each(function () {
            var script = $(this);
            var src = $(this).attr('src');
            onThisPage.push(src);
            if (loadedScripts.indexOf(src) == -1 && src) {
                YnloFramework.log('Loading Script:' + src);
                $('head').append('<script src="' + src + '"></script>');
                loadedScripts.push(src);
            }
        });

        //remove head scripts not required o this page
        //because loaded scripts cant be unloaded is better keep to avoid load again later
        //TODO: any way to unload the loaded scripts and unregister references?
    },
    loadStylesheets: function (output) {
        //already loaded styles to avoid load again
        var loadedStyles = [];
        $(document).find('link').each(function () {
            if ($(this).attr('href')) {
                loadedStyles.push($(this).attr('href'));
            }
        });
        var onThisPage = [];
        $(output).find('link').each(function () {
            var style = $(this);
            var href = $(this).attr('href');
            onThisPage.push(href);
            if (loadedStyles.indexOf(href) == -1 && href) {
                YnloFramework.log('Loading Stylesheet:' + href);
                $('head').append('<link rel="stylesheet" href="' + href + '"/>');
                loadedStyles.push(href);
            }
        });

        //remove head styles not required o this page
        //avoid apply styles for other pages
        $('head').find('link').each(function () {
            var style = $(this);
            var href = $(this).attr('href');
            if (onThisPage.indexOf(href) == -1 && href) {
                YnloFramework.log('Removing Stylesheet:' + href);
                $(style).remove();
            }
        });
    }
};
YnloFramework.register('Pjax');