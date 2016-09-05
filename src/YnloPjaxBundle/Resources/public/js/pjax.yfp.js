/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

YnloFramework.Pjax = {
    config: {
        target: 'body',
        links: 'a:not([data-pjax="false"])a:not([href="#"])a:not([target="_blank"])',
        forms: 'form:not([data-pjax="false"])',
        autospin: true,
        spinicon: 'fa fa-spinner fa-pulse'
    },
    lastLocation: null,
    currentLocation: function () {
        return location.pathname + location.search;
    },
    triggerElement: null,
    init: function () {

        if (this.config.links) {
            this._setupLinks();
        }
        if (this.config.forms) {
            this._setupForms();
        }
        if (this.config.autospin) {
            this._setupAutospin();
        }

        YnloFramework.Pjax.pushState(window.location.href);

        $(window).off('popstate.pjax');
        $(window).on("popstate.pjax", function (event) {
            // http://stackoverflow.com/questions/9731838/how-to-cancel-popstate-in-certain-condition
            if (YnloFramework.Pjax.lastLocation != YnloFramework.Pjax.currentLocation()) {
                YnloFramework.Pjax.load(window.location.href);
            }
        });
    },
    _setupAutospin: function () {
        $(document).on('pjax:start', function () {
            var element = YnloFramework.Pjax.triggerElement;
            if (element) {
                var icon = YnloFramework.Pjax.config.spinicon;
                if (element.find('i').length) {
                    var currentIcon = element.find('i:first');
                    currentIcon.attr('origin-class', currentIcon.attr('class'));
                    currentIcon.attr('class', icon);

                } else if (element.hasClass('btn')) {
                    element.prepend('<i class="pjax-spinicon ' + icon + '" style="margin-right:5px"></i>');
                }
            }
        });
        $(document).on('pjax:success pjax:error pjax:abort', function () {
            var element = YnloFramework.Pjax.triggerElement;
            if (element) {
                element.find('.pjax-spinicon').each(function () {
                    $(this).remove();
                });
                element.find('i').each(function () {
                    if ($(this).attr('origin-class')) {
                        $(this).attr('class', $(this).attr('origin-class'));
                    }
                });
            }
        });
    },
    _setupLinks: function () {
        $(document).off('click.pjax');
        $(document).on('click.pjax', YnloFramework.Pjax.config.links, function (event) {
            var $a = $(this);
            var url = $a.attr("href");

            if (!url || url.substr(0, 1) == '#'
                || YnloFramework.Location.getURLParameter('data-pjax', url)
                || url.substr(0, 11) == 'javascript:'
                || $a.closest('.sf-toolbar').length
            ) {
                return;
            }

            var pjaxLinkEvent = jQuery.Event("pjax:link");
            $a.trigger(pjaxLinkEvent, [url]);
            if (pjaxLinkEvent.isPropagationStopped() || pjaxLinkEvent.isDefaultPrevented()) {
                event.preventDefault && event.preventDefault();
                return false;
            }

            //ignore external domains
            if (url.indexOf('://') > -1 && url.indexOf(document.domain) === -1) {
                return true;
            }

            event.preventDefault && event.preventDefault();

            YnloFramework.Pjax.load(url, $a);

            return false;
        });
    },
    _submitBtn: null,
    _setupForms: function () {

        $(document).on('click', 'button[type="submit"]', function () {
            YnloFramework.Pjax._submitBtn = $(this);
        });

        $(document).off('submit.pjax');
        $(document).on('submit.pjax', YnloFramework.Pjax.config.forms, function (event) {
            var $form = $(this);

            event.preventDefault();

            var url = window.location.href;
            if ($form.attr('action')) {
                url = $form.attr('action');
            }

            var $submitBtn = YnloFramework.Pjax._submitBtn;
            if ($submitBtn && $submitBtn.attr('name')) {
                var $hidden = $('<input type="hidden">');
                $hidden.attr('name', $submitBtn.attr('name'));
                $hidden.attr('value', $submitBtn.attr('value'));
                $form.append($hidden);
            }

            require(['jquery_form'], function () {
                $form.ajaxSubmit({
                    beforeSend: function (xhr) {
                        YnloFramework.Pjax._xhr = xhr;
                        xhr.setRequestHeader("X-PJAX", 'true');
                        $(document).trigger('pjax:start', [xhr]);
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
                    error: function (event, xhr, reason) {
                        if (reason == 'abort') {
                            return;
                        }
                        $(document).trigger('pjax:error', [event, xhr, reason]);
                    }
                });
            });
        })
    },
    _xhr: null,
    refresh: function () {
        YnloFramework.Pjax.load(window.location.href);
    },
    load: function (url, triggerElement) {
        if (YnloFramework.Pjax._xhr) {
            $(document).trigger('pjax:abort', [YnloFramework.Pjax._xhr]);
            YnloFramework.Pjax._xhr.abort();
        }

        //element triggering the load, for autospin
        if (triggerElement) {
            YnloFramework.Pjax.triggerElement = triggerElement;
        }

        YnloFramework.Pjax._xhr = $.ajax({
            url: url,
            beforeSend: function (xhr) {
                xhr.setRequestHeader("X-PJAX", 'true');
                $(document).trigger('pjax:start', [xhr]);
            },
            success: function (output, status, xhr) {

                if (typeof xhr.getResponseHeader === 'function') {
                    if (xhr.getResponseHeader('X-PJAX-URL')) {
                        url = xhr.getResponseHeader('X-PJAX-URL');
                    }
                }

                if (YnloFramework.hasPlugin('Modal')) {
                    if (typeof xhr.getResponseHeader === 'function') {
                        if (xhr.getResponseHeader('X-MODAL')) {
                            require(['bootstrap-dialog'], function (BootstrapDialog) {

                                //hack required to force parse elements inside modals
                                //and avoid a flicker while is creating the modal
                                var message = $('<div class="col-md-12">' + output.message + '</div>');
                                $('body').append(message);
                                $(document).trigger('ajaxSuccess', [output, status, xhr]);

                                var options = $.extend({}, YnloFramework.Modal.config, output);

                                options.message = message;

                                var dialog = new BootstrapDialog(options);
                                YnloFramework.Modal.setOptions(dialog, options); //required to parse options like actions

                                dialog.open();

                                //set the form action url in case origin don`t have
                                var form = dialog.getModalBody().find('form');
                                if (form.length && !form.attr('action')) {
                                    form.attr('action', url);
                                }
                                $(document).trigger('pjax:abort', [output, status, xhr]);
                            });
                            return;
                        }
                    }
                }

                YnloFramework.Pjax.pushResponse(url, output);

                $(document).trigger('pjax:success', [output, status, xhr]);
            },
            error: function (event, xhr, reason) {
                if (reason == 'abort') {
                    return;
                }
                $(document).trigger('pjax:error', [event, xhr, reason]);
            }
        });
    },
    pushState: function (url) {
        window.history.pushState({id: (new Date).getTime(), url: url}, '', url);
        YnloFramework.Pjax.lastLocation = YnloFramework.Pjax.currentLocation();
    },
    pushResponse: function (url, response) {
        if (YnloFramework.Pjax.lastLocation !== url){
            window.scrollTo(0,0);
        }
        YnloFramework.Pjax.pushState(url);

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
        var originClass = null;
        if ($(response).find(target).length > 0) {
            innerResponse = $(response).find(target).html();
            originClass = $(response).find(target).attr('class');

        }

        if (YnloFramework.Pjax.config.target !== 'body') {
            $('.pace').remove();//avoid a blink in pjax loader
        }

        $(YnloFramework.Pjax.config.target).html(innerResponse);
        $(YnloFramework.Pjax.config.target).attr('class', originClass);

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
                var src = $(this).attr('src');
                loadedScripts.push(YnloFramework.Pjax.__getRealAssetUrl(src));
            }
        });

        var onThisPage = [];
        $(output).find('script').each(function () {
            var script = $(this);
            var src = YnloFramework.Pjax.__getRealAssetUrl($(this).attr('src'));
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
                loadedStyles.push(YnloFramework.Pjax.__getRealAssetUrl($(this).attr('href')));
            }
        });
        var onThisPage = [];
        $(output).find('link').each(function () {
            var style = $(this);
            var href = YnloFramework.Pjax.__getRealAssetUrl($(this).attr('href'));
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
            var href = YnloFramework.Pjax.__getRealAssetUrl($(this).attr('href'));
            if (onThisPage.indexOf(href) == -1 && href) {
                YnloFramework.log('Removing Stylesheet:' + href);
                $(style).remove();
            }
        });
    },
    //remove any parameter passed to the script url
    //used in dev environment to avoid load already loaded script
    //in dev env scripts and css has a timestamp to force assetic reload when page is refreshed
    __getRealAssetUrl: function (src) {
        if (YnloFramework.debug && src) {
            var regExp = /\?.+/;
            src = src.replace(regExp, '');
        }

        return src;
    }
};
YnloFramework.register('Pjax');
