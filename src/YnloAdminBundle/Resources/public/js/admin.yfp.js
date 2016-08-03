/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

YnloFramework.Admin = {
    config: {
        icheck: 'square-blue'
    },
    init: function () {
        $(document).on('pjax:success', function () {
            YnloFramework.Admin.initAdminLTE();
            Admin.shared_setup(YnloFramework.Pjax.config.target)
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
    }
};