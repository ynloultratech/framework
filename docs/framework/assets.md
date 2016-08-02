## Assets

**YnloFramework** use [AsseticBundle](https://symfony.com/doc/current/cookbook/assetic/asset_management.html#introducing-assetic) to work with assets (.js, .css).
Then is required to add assetic bundle to your Kernel before use the framework.

## Wy use assetic?

Commonly is a tedious task add a lot of javascript and stylesheets lines to include all required libraries.
With YnloFramework is not required any more, at least internal libraries, are included automatically and compiled using assetic.

Assetic is required to load assets according of enabled bundles or libraries, 
and pass some configuration from your config.yml to javascript plugins. 

##### e.g.

_config.yml:_
````yml
ynlo_framework:
    pace: false
````

According to this config the framework don't compile assets related to [Pace](github.hubspot.com/pace/docs/welcome/). 
Others bundles can add custom assets to the framework in order to work properly.

## How its works?

The framework work with **contexts**, each context represent a set of assets to use. 
Each context is like a part of your site using diferents assets and styles. 
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
Contexts are declared in the `ynlo_framework` configuration under the `assets_contexts:` key.

##### e.g.

````yml
ynlo_framework:
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

In the above example a new context `frontend` is created to override default bootstrap css and other context called `mobile`
based on the previous `frontend` context but excluding some assets and adding new ones.

Each context is composed by a lot of assets.
YnloFramework create a named asset for each internal bundle in order to use assets separated, and other assets for third-party libraries.

> To view the entire list of named assets and content, can use `php bin/console assetic:assets`





