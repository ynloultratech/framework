# Assets

Commonly is a tedious task add a lot of javascript and stylesheets lines to include all required libraries.
With YnloFramework is not required any more, at least internal libraries, are included automatically and compiled using assetic.

## Context

The framework work with **contexts**, each context represent a set of assets to use. 
Each context is like a part of your site using different assets and styles. 
The administration and the frontend is a good example of different contexts. 
By default the framework comes with one default context called `app`. 
To use this context in the view the only that yo need is suffix the context name with the
type of assets to include.

##### e.g.
````twig
{% stylesheets '@app_css' filter='cssrewrite' output='css/compiled/app.css' %}
    <link rel="stylesheet" href="{{ asset_url }}"/>
{% endstylesheets %}

{% javascripts '@app_js' output='js/compiled/app.js' %}
    <script src="{{ asset_url }}"></script>
{% endjavascripts %}
````   

> Contexts are assetic [named assets](https://symfony.com/doc/current/cookbook/assetic/asset_management.html#using-named-assets)
and are created automatically when the application is compiled.

### Customizing a context:

Can create your own context or simply customize any existent context. 
Contexts are declared in the `ynlo_assets` configuration under the `contexts:` key.

##### e.g.

````yml
ynlo_assets:
    contexts:
        frontend:
            include:
                - all
            override: 
                bootstrap_css: path/to/bootstrap_themed.css
        mobile:
           include:
               - all
               - 'js/mobile_ui.css' 
           exclude:
               - bundle_ynlo_modal
               - bundle_ynlo_pjax
               - bundle_ynlo_admin  
                               
````

##### Usage:
````twig
{% stylesheets '@frontend_css' filter='cssrewrite' %}
    <link rel="stylesheet" href="{{ asset_url }}"/>
{% endstylesheets %}

{% javascripts '@frontend_js' %}
    <script src="{{ asset_url }}"></script>
{% endjavascripts %}
````   

## Assets

YnloFramework work with named assets, then can register one asset with a name to reuse later in others contexts.

##### e.g.

````yml
ynlo_assets:
    assets:
        jquery: js/jquery.min.js 
    contexts:
        frontend:
            include:
                - jquery
        mobile:
            include: 
                - jquery
                               
````

> All registered assets are compiled into only one file, with many asset declared you will be have a big asset file for each context, to avoid this use modules form some assets.

## Javascript Modules

Modules is like a asset, but is not compiled inside the asset file included in your template. 
Otherwise asset files registered as modules will be loaded when are needed.

##### e.g.

````yml
ynlo_assets:
    contexts:
        frontend:
            include:
                - bootstrap_css: css/bootstrap.min.css
    modules:
        bootstrap:
           asset: js/bootstrap.min.js
````
In the above example only the `.css` is loaded when the page load, the `.js` file is registered as module and only is loaded when is needed.

##### e.g.
````javascript
<script type="text/javascript">
    $(document).ready(function () {
        require(['bootstrap'], function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
        });
    });
</script>
````

This functionally is thanks to [RequireJs](http://requirejs.org/) and increase the page load speed loading only that you need when you need.

Some advanced configuration can be used if is needed, like declare module dependencies.
````yml
ynlo_assets:
    modules:
        datetimepicker:
           asset: js/bootstrap_datetime_picker.min.js
           deps: 
               - bootstrap
````

#### Jquery Plugin as Modules

Can load jQuery plugins as modules and then use when is needed like described above. 
Alternatively jQuery modules can be loaded automatic when is used.

YnloFramework has an auto-loader for jQuery modules, the only that you need is specify jquery plugins inside a given asset:

````yml
ynlo_assets:
    modules:
        datetimepicker:
           asset: js/bootstrap_datetime_picker.min.js
           deps: 
              - bootstrap
           jquery_plugins:
              - datetimepicker
````

Then instead of use:

````javascript
require(['datetimepicker'], function () {
    $('#date').datetimepicker();
});
````
Simply use: 

````javascript
$('#date').datetimepicker();
````

Assets automatically detect the use of  `datetimepicker` and load the related asset to use.

> NOTE: This feature is only available for jQuery plugins and may not work in all cases.