// Override some part of Admin js from sonata without lost all functions

Admin.log = function (subject) {
    var msg = '[Admin] ' + Array.prototype.join.call(arguments, ', ');
    if (YnloFramework.Core.config.debug) {
        console.log(msg);
    }
};

Admin.setup_icheck = function (subject) {
    if (window.SONATA_CONFIG && window.SONATA_CONFIG.USE_ICHECK && YnloFramework.Admin.config.icheck) {
        Admin.log('[core|setup_icheck] configure iCheck on', subject);
        //YnloFramework.Admin.config.icheck = 'futurico';
        jQuery("input[type='checkbox']:not('label.btn>input'), input[type='radio']:not('label.btn>input')", subject).iCheck({
            checkboxClass: 'icheckbox_' + YnloFramework.Admin.config.icheck,
            radioClass: 'iradio_' + YnloFramework.Admin.config.icheck
        });
    }
};