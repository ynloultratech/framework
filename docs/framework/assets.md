## Assets

**YnloFramework** use [AsseticBundle](https://symfony.com/doc/current/cookbook/assetic/asset_management.html#introducing-assetic) to work with assets (.js, .css).
Then is required to add assetic bundle to your Kernel before use the framework.

## Wy use assetic?

Assetic is required to load assets according of enabled bundles or libraries, 
and pass some configuration from your config.yml to javascript plugins. 

##### e.g.

_config.yml:_
````yml
ynlo_framework:
    pace: false
````

According to this config the framework don't compile assets related to [Pace](github.hubspot.com/pace/docs/welcome/). 
Others bundles can add custom assets to the framework in order to work fine.

## Using framework assets

When the framework is compiled automatically create a set of [Named Assets](https://symfony.com/doc/current/cookbook/assetic/asset_management.html#using-named-assets)
with all required assets to work fine.

By default some named assets has been created:

##### e.g.
- **@ynlo_framework_all_css:** contains all css _(Framework core and other framework bundles if are enabled)_
- **@ynlo_framework_all_ks:** contains all js _(Framework core and other framework bundles if are enabled)_

YnloFramework create a named asset for each internal bundle in order to use assets separated, and other assets for third-party libraries.

##### e.g.
- **jquery**: jquery minimized version
- **bundle_ynlo_modal_js**: all javascript assets used by YnloModalBundle

**Head Ups!**: Don't use '@ynlo_framework_all_js' with other named asset like `@bundle_ynlo_modal_js`, because duplicate assets were created.

> To view the entire list of named assets and content, can use `php bin/console assetic:assets`

## Usage

In your template:

````twig
{% stylesheets '@ynlo_framework_all_css' 'css/my__custom__style.css' filter='cssrewrite' output='css/compiled/app.css' %}
    <link rel="stylesheet" href="{{ asset_url }}"/>
{% endstylesheets %}

{% javascripts '@ynlo_framework_all_js' output='js/compiled/app.js' %}
    <script src="{{ asset_url }}"></script>
{% endjavascripts %}
````   

> `@ynlo_framework_all_css` and `@ynlo_framework_all_js` are recompiled based on real assets required by each internal bundle.
 And its recommended for most use cases.
