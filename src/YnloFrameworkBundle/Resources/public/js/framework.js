/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

/**
 * Plugin Prototype
 *
 * YnloFramework.PluginName = {
 *      init: function () {
 *      },
 *      config: {}
 *   };
 */

var YnloFramework = {
    plugins: [],
    initialized: false,
    debug: false,
    initializedPlugins: [],
    register: function (plugin) {
        if (!this.hasPlugin(plugin)) {
            this.plugins.push(plugin);

            if (this.initialized) YnloFramework.initPlugin(plugin);
        }
    },
    hasPlugin: function (plugin) {
        return this.plugins.indexOf(plugin) > -1
    },
    isPluginInitialized: function (plugin) {
        return this.initializedPlugins.indexOf(plugin) > -1
    },
    //star all plugins
    init: function () {
        //initialize all registered plugins
        for (var index in this.plugins) {
            var plugin = this.plugins[index];
            if (!YnloFramework.isPluginInitialized(plugin)) {
                YnloFramework.initPlugin(plugin);
                YnloFramework.initializedPlugins.push(plugin);
            }
        }

        YnloFramework.initialized = true;
    },
    initPlugin: function (plugin) {
        if (typeof YnloFramework[plugin]['init'] == 'function') {
            YnloFramework[plugin].init();
            YnloFramework.log('YnloFramework: initializing plugin "' + plugin + '"');
        }
    },
    log: function (msg) {
        if (YnloFramework.debug) {
            console.log(msg);
        }
    },
    info: function (msg) {
        if (YnloFramework.debug) {
            console.info(msg);
        }
    },
    warn: function (msg) {
        if (YnloFramework.debug) {
            console.warn(msg);
        }
    },
    error: function (msg) {
        if (YnloFramework.debug) {
            console.error(msg);
        }
    }
};

$(document).on('ready', function () {
    YnloFramework.init();
});
