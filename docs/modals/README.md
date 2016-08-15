## Modals

Modals allow view or edit content using bootstrap modals.

### Installation

Build the kernel with modal support.

````php
 AppBuilder::setUp($this)->withModals();
```` 

### Usage

#### Creating a modal in a controller:

Add the following trait to your controller: `YnloFramework\YnloModalBundle\Controller\ModalControllerTrait`

````php
/**
 * @Route(path="/my_modal", name="my_modal", de)
 */
public function myModalAction()
{
    $modal = $this->createModal('modals/my_modal.html.twig', [], 'My Modal');

    return $this->renderModal($modal);
}
````

#### Launching the modal

Have some diferents way to call a modal action.

##### Using link attributes

````html
<a data-target="modal" href="{{ path('my_modal') }}">My Modal</a>
````

##### Using url parameters

````html
<a href="{{ path('my_modal',{"data-target":"modal"}) }}">My Modal</a>
````

##### Using Pjax

````html
<a href="{{ path('my_modal') }}">My Modal</a>
````

> NOTE: if you are using [Pjax](../pjax/README.md) is not required add any attributes to the link. Pjax automatically open a modal based on the server response.