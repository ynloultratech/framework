/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
$.holdReady(true);
YnloFramework.Admin = {
    config: {
        icheck: 'square-blue'
    },
    init: function () {
        require(['admin_lte', 'sonata_admin'], function () {
            //the sonata admin has onReady event, then to override sonata config is required
            //hold the ready and release after override
            YnloFramework.Admin.overrideSonataAdmin();

            $.holdReady(false);

            $(document).ready(function () {
                $('[data-toggle="tooltip"]').tooltip();
                $('[data-toggle="popover"]').popover();
            });

            if (YnloFramework.hasPlugin('Pjax')) {
                //ignore links with sonata-ba-action class in pjax
                //e.g. (Add New) in a one to many form
                YnloFramework.Pjax.config.links = YnloFramework.Pjax.config.links + 'a:not(.sonata-ba-action)'
            }

            $(document).on('pjax:success', function () {
                YnloFramework.Admin.initAdminLTE();
                Admin.shared_setup($(YnloFramework.Pjax.config.target))
            });

            _init();
        });
    },
    //this initialization is a copy of initialization
    //from AdminLTE.js, the main target is reinitialize
    //the admin on every pjax request
    initAdminLTE: function () {

        //Fix for IE page transitions
        $("body").removeClass("hold-transition");

        //Extend options if external options exist
        if (typeof AdminLTEOptions !== "undefined") {
            $.extend(true,
                $.AdminLTE.options,
                AdminLTEOptions);
        }

        //Easy access to options
        var o = $.AdminLTE.options;

        ///NOTE! the following statements differ from
        //the original in the AdminLTE initialization
        //and are used to do some stuff before restart the admin when use pjax

        $(document).off('click', o.sidebarToggleSelector); //disconnect the click to allow reconnect
        log('Reinitialize AdminLTE');

        ///END of different statements


        //Set up the object
        _init();

        //Activate the layout maker
        $.AdminLTE.layout.activate();

        //Enable sidebar tree view controls
        $.AdminLTE.tree('.sidebar');

        //Enable control sidebar
        if (o.enableControlSidebar) {
            $.AdminLTE.controlSidebar.activate();
        }

        //Add slimscroll to navbar dropdown
        if (o.navbarMenuSlimscroll && typeof $.fn.slimscroll != 'undefined') {
            $(".navbar .menu").slimscroll({
                height: o.navbarMenuHeight,
                alwaysVisible: false,
                size: o.navbarMenuSlimscrollWidth
            }).css("width", "100%");
        }

        //Activate sidebar push menu
        if (o.sidebarPushMenu) {
            $.AdminLTE.pushMenu.activate(o.sidebarToggleSelector);
        }

        //Activate Bootstrap tooltip
        if (o.enableBSToppltip) {
            $('body').tooltip({
                selector: o.BSTooltipSelector
            });
        }

        //Activate box widget
        if (o.enableBoxWidget) {
            $.AdminLTE.boxWidget.activate();
        }

        //Activate fast click
        if (o.enableFastclick && typeof FastClick != 'undefined') {
            FastClick.attach(document.body);
        }

        //Activate direct chat widget
        if (o.directChat.enable) {
            $(document).on('click', o.directChat.contactToggleSelector, function () {
                var box = $(this).parents('.direct-chat').first();
                box.toggleClass('direct-chat-contacts-open');
            });
        }

        /*
         * INITIALIZE BUTTON TOGGLE
         * ------------------------
         */
        $('.btn-group[data-toggle="btn-toggle"]').each(function () {
            var group = $(this);
            $(this).find(".btn").on('click', function (e) {
                group.find(".btn.active").removeClass("active");
                $(this).addClass("active");
                e.preventDefault();
            });

        });
    },
    overrideSonataAdmin: function () {
        Admin.log = function (subject) {
            var msg = '[Admin] ' + Array.prototype.join.call(arguments, ', ');
            if (YnloFramework.Core.config.debug) {
                console.log(msg);
            }
        };

        Admin.setup_icheck = function (subject) {
            if (window.SONATA_CONFIG && window.SONATA_CONFIG.USE_ICHECK && YnloFramework.Admin.config.icheck) {
                Admin.log('[core|setup_icheck] configure iCheck on', subject);
                jQuery("input[type='checkbox']:not('label.btn>input'), input[type='radio']:not('label.btn>input')", subject).each(function () {
                    var element = $(this);
                    require(['icheck'], function () {
                        element.iCheck({
                            checkboxClass: 'icheckbox_' + YnloFramework.Admin.config.icheck,
                            radioClass: 'iradio_' + YnloFramework.Admin.config.icheck
                        });
                    });
                })
            }
        };

        var select2fallback = Admin.setup_select2;
        Admin.setup_select2 = function (subject) {
            if (jQuery('select:not([data-sonata-select2="false"])not:select2', subject).length == 0) {
                return;
            }
            require(['select2'], function () {
                select2fallback(subject);
            });
        };

        var editableOriginalFunction = Admin.setup_xeditable;
        Admin.setup_xeditable = function (subject) {
            if (jQuery('.x-editable', subject).length == 0) {
                return;
            }
            editableOriginalFunction(subject);
        };
    }
};