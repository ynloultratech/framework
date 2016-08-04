## Custom Admin Action

Custom admin actions works exactly like is described in the following docs: [Sonata Docs.](https://sonata-project.org/bundles/admin/master/doc/cookbook/recipe_custom_action.html)
The only changes and added features is described below.

### Predefined Templates

Can omit the step of create the template for each action, now the admin do this automatically.

````php
protected function configureListFields(ListMapper $listMapper)
{
    $listMapper

         // other fields...

        ->add('_action', 'actions', array(
            'actions' => array(

                // ...

                'clone' => array(
                    'icon' => 'fa fa-clone',
                    'type' => 'warning', //add bs class btn-warning
                    'role' => 'CLONE' // the role to verify for permissions in this admin
                )
            )
        ))
    ;
}
````

> The `template` option can be used too and is useful when you need more complex logic.

````php
'clone' => array(
        'template' => 'list_action_clone.html.twig',
    )
````


### Dropdown


Alternative to this you can create a dropdown with all actions.

````php
protected function configureListFields(ListMapper $listMapper)
{
    $listMapper

         // other fields...

        ->add('_action', 'actions', array(
            'dropdown' => true, // create a dropdown with each action as menu item
            'actions' => array(

                // ...

                'clone' => array(
                    'icon' => 'fa fa-clone',
                    'role' => 'CLONE' // the role to verify for permissions in this admin
                )
            )
        ))
    ;
}
````

#### Visible

In order to define when some action is visible can use the `visible` option.
This allow more complex logic for any action.

````php
'clone' => array(
     //..
    'visible' => $this->isGranted("ROLE_MANAGER")
    )
````
or if you need the action based on the record

````php
'clone' => array(
     //..
    'visible' => function($object){
            return $object->isClonable()
        }
    )
````

#### Added General Options

- **dropdown**: *(boolean)* create a dropdown for all actions
- **dropdown_icon**: *(boolean)* icon to use in the dropdown (default: fa fa-navicon)

#### Added Options for each action

- **visible**: *(boolean|callable)* define if the action should be visible or not
- **icon**: *(string)* class icon to add inside `<i>` tag
- **type**: *(string)*Bootstrap class to define type, `default`, `warning`, etc.
- **role**: *(string)*the role to verify for permissions in this admin. is used with `{{ admin.isGranted(ROLE_NAME) }}` in the template, by default uppercase version of action name.
- **divider**: *(string)* add visual divider for each action, can be: `prepend`, `append` or `both`
- **separator**: *(string)* add NON visual divider for each action, can be: `prepend`, `append` or `both` *(not functional for dropdowns)*
