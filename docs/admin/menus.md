
## Menus

All menus are created using the [KnpMenuBundle](http://symfony.com/doc/current/bundles/KnpMenuBundle/index.html).

The template have some default positions to create menus

- **sidebar** - It's the main menu located at the left of the screen
- **navbar-left** - It's empty by default and is placed on the left of the top navbar
- **navbar-right** - It's the menu containing the user name and logout action

## Bundle configuration

This is the default configuration for all menus inside `ynlo_admin` configuration.

````yml
menu:
    main:                 'ToplibAdminBundle:Builder:mainMenu'
    navbar_left:          'ToplibAdminBundle:Builder:navbarLeftMenu'
    navbar_right:         'ToplibAdminBundle:Builder:navbarRightMenu'
````

YnloAdmin use [the easy way](http://symfony.com/doc/current/bundles/KnpMenuBundle/index.html#method-a-the-easy-way-yay)
of KnpMenu to create all menus. It`s easy to customize and override.

## Create a custom menu for `navbar-left` position

Create your menu builder class inside `Menu` folder in your bundle.
Can extends from `YnloFramework\\YnloAdminBundle\\Menu\\Builder`.

````php
namespace AppBundle\Menu;

use Knp\\Menu\\FactoryInterface;
use YnloFramework\\YnloAdminBundle\\Menu\\Builder as BaseBuilder;

class Builder extends BaseBuilder
{

    public function navbarLeftMenu(FactoryInterface $factory, array $options)
    {
        $menu = parent::navbarLeftMenu($factory, $options);

        #customize your menu
        $menu->addChild('Dashboard',['route'=>'admin_dashboard']);

        return $menu;
    }
}
````

Override the toplib default menu with your menu in the `config.yml`

````yml
#config.yml
ynlo_admin:
    #...
    menu:
        navbar_left: AppBundle:Builder:navbarLeftMenu
````

It's all, enjoy your new menu.

> NOTE: Can override all menus following the above steps.

## Menu extra features

Some extra features has been added to knpMenu to create more custom and powerful menus.

> NOTE: Those features are only available if you use the default menu template

### Divider

Add divider before or after each item.

##### Usage

Before:
````php
   $menu->addChild('users', ['route' => 'admin_security_users'])->setExtra('divider', 'prepend');
````
After:
````php
   $menu->addChild('users', ['route' => 'admin_security_users'])->setExtra('divider', 'append');
````
Before and After:
````php
   $menu->addChild('users', ['route' => 'admin_security_users'])->setExtra('divider', 'both');
````

### Icon

Add custom icon inside `i` tag just before the element name.

##### Usage

````php
   $menu->addChild('users', ['route' => 'admin_security_users'])->setExtra('icon', 'fa fa-users');
````

### Badge

Highlight new or unread menu items by adding an badge.

##### Usage

````php
   $badge = [
    'type'=> 'warning',
    'value'=> 12,
    'animation'=> 'flash'
   ];
   $menu->addChild('pending_orders', ['route' => 'admin_pending_orders'])->setExtra('badge', $badge);
````

##### Options

- **type** - type of the badge, `default`, `warning`, `primary`, `success` or `danger`
- **value** - value inside the badge
- **animation** - name of the animation to use, see: [CS3 Animations](https://daneden.github.io/animate.css/)

### Remote (Dropdown)

Render custom dropdown content based on the response of a remote action.

##### Usage
````php
$menu->addChild('pending_posts', ['uri' => '#'])
    ->setAttribute('dropdown', true)
    ->setExtra('icon', 'fa fa-inbox')
    ->setExtra('remote', 'admin_post_last_unpublished_list');
````

The `remote` value can be the name of the route (`routeName`)
or the name with params in a array (`[ routeName, arrayOfParams ]`)

> NOTE: only works with drop-downs

### Template

Render custom template inside menu item. Helpful to create mega menus.

The template should be declared using two formats:  `templateName` or `[ temlateName, arrayOfParams ]`

##### Usage

Passing only the template name to render static data
````php
   $menu->addChild('advise')->setExtra('template','advise.html.twig');
````

Passing the template name and all required parameters to render dynamic templates
````php
   $params = ['user'=> $user];
   $menu->addChild('logout')->setExtra('logout',['logout.html.twig', $params]);
````

> NOTE: The name of the menu will not be rendered

### Animation

Use CSS3 animations to display dropdown menus, see: [CS3 Animations](https://daneden.github.io/animate.css/)

##### Usage

````php
$menu->addChild('dropdown', ['uri' => '#'])
    ->setAttribute('dropdown', true)
    ->setExtra('icon', 'fa fa-list')
    ->setExtra('animation', 'lightSpeedIn');
````php



