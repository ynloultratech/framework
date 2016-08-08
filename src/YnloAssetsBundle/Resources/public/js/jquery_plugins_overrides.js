/*
 This file override some jquery plugins to load the
 plugin file(.js) using RequireJs when the plugin is used
 without the need of declare the require statement

 e.g.

 BEFORE:
 require(['jquery_ui'], function () {
 $('element').sortable(options);
 });

 AFTER:
 $('element').sortable(options);

 This file is written dynamically using assetic

 */
