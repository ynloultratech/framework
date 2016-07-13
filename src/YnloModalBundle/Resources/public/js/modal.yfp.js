/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * Requirements:
 * library: https://nakupanda.github.io/bootstrap3-dialog
 */

YnloFramework.Modal = {
    init: function () {
        YnloFramework.Modal._setupLinks();
        BootstrapDialog.nl2br = false;
    },
    config: {
        spinicon: 'fa fa-spinner fa-pulse',
        loaderTemplate: '<div class="loader loader-lg"></div>',
        loaderDialogClass: 'modal-remote-loader',
        titleTemplate: "<h4 class='modal-title'>%icon% %title%</h4>",
        size: 'size-normal',
        nl2br: false,
        urlDataTarget: 'data-target',
        urlDataRefresh: 'data-refresh',
        urlDataRedirect: 'data-redirect',
        linkDataTarget: 'data-target',
        linkDataRefresh: 'data-refresh',
        linkDataRedirect: 'data-redirect'
    },
    _setupLinks: function () {

        if (YnloFramework.hasPlugin('Pjax')){
            $(document).off("pjax:link.modal", 'a');
            $(document).on("pjax:link.modal", 'a', function (event) {
                var options = YnloFramework.Modal._extractPopupOptions($(this));
                var url = $(this).attr('href');
                if (options.target == 'modal') {

                    event.preventDefault && event.preventDefault();
                }
            });
        }

        $(document).off("click.modal", 'a');
        $(document).on("click.modal", 'a', function (event) {
            var options = YnloFramework.Modal._extractPopupOptions($(this));
            var url = $(this).attr('href');
            if (options.target == 'modal') {
                event.preventDefault && event.preventDefault();
                var dialog = new BootstrapDialog({
                    message: $(YnloFramework.Modal.config.loaderTemplate),
                    closable: false,
                    cssClass: YnloFramework.Modal.config.loaderDialogClass,
                    nl2br: false
                });
                dialog.open();
                $.ajax({
                    url: url,
                    success: function (response) {
                        YnloFramework.Modal.setOptions(dialog, response);
                        var form = dialog.getModalBody().find('form');

                        if (form.length) {
                            form.attr('action', url);
                        }
                        dialog.getModal().removeClass(YnloFramework.Modal.config.loaderDialogClass);
                        dialog.open();
                    },
                    error: function () {
                        dialog.close();
                    }
                });

            }
        });
    },
    _extractPopupOptions: function ($anchor) {
        var url = $anchor.attr('href');

        //popup based on link information
        var inUrl = {
            'target': YnloFramework.Location.getURLParameter('data-target', url),
            'refresh': YnloFramework.Location.getURLParameter('data-popup-refresh', url),
            'redirect': YnloFramework.Location.getURLParameter('data-popup-redirect', url)
        };

        //popup based on data information
        var inData = {
            'target': $anchor.data('target'),
            'refresh': $anchor.data('popup-refresh'),
            'redirect': $anchor.data('popup-redirect')
        };

        //merge
        return {
            'target': inData.target && inData.target != 'undefined' ? inData.target : inUrl.target,
            'refresh': inData.refresh && inData.refresh != 'undefined' ? inData.refresh : inUrl.refresh,
            'redirect': inData.redirect && inData.redirect != 'undefined' ? inData.redirect : inUrl.redirect
        };
    },
    _submitAction: function (dialog) {
        var form = dialog.getModalBody().find('form');
        //get the current action in case the origin don`t have
        var url = form.attr('action');
        form.ajaxSubmit({
            beforeSend: function () {
                dialog.enableButtons(false);
                dialog.setClosable(false);
            },
            success: function (response) {
                if (!response || response.result == 'ok') {
                    dialog.close();
                } else {
                    dialog.enableButtons(true);
                    dialog.setClosable(true);
                    YnloFramework.Modal.setOptions(dialog, response);
                    var form = dialog.getModalBody().find('form');

                    if (form.length) {
                        //set the action url in case origin don`t have
                        form.attr('action', url);
                    }
                }
            }
        })
    },
    setOptions: function (dialog, options) {
        var index;
        for (index in options.buttons) {

            if (!options.buttons.hasOwnProperty(index)) {
                return;
            }

            //parse action for each button
            var action = options.buttons[index].action;
            if (action) {
                switch (action) {
                    case 'close':
                        action = function (dialog) {
                            dialog.close()
                        };
                        break;
                    case 'submit':
                        action = YnloFramework.Modal._submitAction;
                        break;
                    default:
                        action = new Function('dialog', options.buttons[index].action);

                }
                options.buttons[index].action = action;
            }
        }

        options = $.extend({}, this.config, options);
        var option;
        for (option in options) {
            var method;

            if (!options.hasOwnProperty(option)) {
                return;
            }

            var value = options[option];

            switch (option) {
                case 'onshown':
                    method = 'onShown';
                    break;
                case 'onhidden':
                    method = 'onHidden';
                    break;
                default:
                    //try setter
                    method = String('set_' + option).toCamelCase();
            }

            if (typeof dialog[method] == 'function') {
                dialog[method](value);
            }
        }
    },
    remote: function (url, options) {
        if (options == undefined) {
            options = {};
        }

        var defaults = {
            message: function (dialog) {
                dialog.enableButtons(false);
                dialog.setClosable(false);
                return dialog.getModalBody().load(url, function () {
                    dialog.enableButtons(true);
                    dialog.setClosable(true);
                });
            }
        };
        BootstrapDialog.show($.extend({}, this.config, defaults, options));
    }
};