## ProgressBar

_AppBundle\ProgressBar\CustomProgressBar.php:_
```php
class CustomProgressBar extends ProgressBar
{
    private $userData;
}
```

_AppBundle\Controller\CustomController.php:_
```php
public function configAction()
{
    $progressbar = new CustomProgressBar(23);
    $progressbar->setTitle('Custom Process');
    $progressbar->setProgressRoute('admin_custom_process');
    $progressbar->setUserData('foo');
    
    $this->get('ynlo.progressbar_manager')->save($progressbar);
    
    return $this->render('AppBundle:Custom:process.html.twig', ['progressbar' => $progressbar]);
}
```

_AppBundle\Resources\views\Custom\process.html.twig:_
```twig
{# ... #}
{{ progressbar_widget(progressbar) }}
{# ... #}
```

_AppBundle\Controller\CustomController.php:_
```php
/**
  * @Route("/admin/custom/process", name="admin_custom_process")
  */
public function processAction(CustomProgressBar $progressbar)
{
    $data = $progressbar->getUserData();
    
    //...
    
    $progressbar->advance();
    
    return $progressbar;
}
```
