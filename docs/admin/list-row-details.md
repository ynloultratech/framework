## List Row Details

Row details allow expand a row in a list in order to show detailed information for related record.

### Defining details

````php
 protected function configureListFields(ListMapper $list)
 {
    //...
    $list->add(
            '_details', 'details', [
                'details_template' => 'list_details.html.twig'
            ]
        );
 }
````

### Creating the template

Create the file named `list_details.html.twig` with your template content. eg:
````twig

 Details of {{ object.name }}

````

### With Ajax

````php
 protected function configureListFields(ListMapper $list)
 {
    //...
    $list->add(
            '_details', 'details', [
                'details_template' => 'list_details.html.twig',
                'ajax' => true,
            ]
        );
 }
````

> Setting ajax true the template will be loaded using ajax, recommended for cases of long details with much information.

### Creating custom action

Can create your custom logic overriding the `detailsAction`.

First you need to create your own Controller extending the one from `YnloFramework\YnloAdminBundle\Controller\CRUDController`

Use the following doc to create it: [Sonata Docs](https://sonata-project.org/bundles/admin/master/doc/cookbook/recipe_custom_action.html)

> IMPORTANT: your controller should extends from `YnloFramework\YnloAdminBundle\Controller\CRUDController` intead of SonataAdmin

After create your controller and adding it to your admin service definition can override the `detailsAction`.

### Field Settings

- **details_template:** The template to expand details
- **ajax:** (default: false) Use ajax or not to show expand details

Others options can be used like other sonata fields. [Sonata Docs](https://sonata-project.org/bundles/admin/master/doc/reference/action_list.html#customizing-the-fields-displayed-on-the-list-page)


