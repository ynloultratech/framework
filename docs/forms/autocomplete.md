## Autocomplete Extension

Autocomplete extensions is used to load remote data into form widget.

### Usage:

#### Basic usage

````php
$form->add(
    'country', EntityType::class,
    [
        'autocomplete' => 'name',
        'class' => Country::class
    ]
);
````
The above statements create a form widget using [Select2](https://select2.github.io/)
with autocomplete. The `autocomplete` option should contain the property name to filter when the user search. 
Can specify more than one property in array.

#### Custom Query

````php
$form->add(
    'country', EntityType::class,
    [
        'autocomplete' => ['name','iso'],
        'autocomplete_max_results'=> 10,
        'class' => Country::class,
        'query_builder' => function ($em) {
            return $em->createQueryBuilder()
                ->select('c')
                ->from(Country::class, 'c')
                ->where('c.continent = 1');
        },
    ]
);
````
The above statement filter the country list by `continent = 1`. 
Is very helpful in some case create a custom query builder to filter results.
But if the continent depends on another select present in the form, in that case
can use `autocomplete_related_fields` to create a chained select.

#### Chained

````php
$form->add(
    'continent', EntityType::class,
          [
              'autocomplete' => 'name',
              'class' => Continent::class
          ]
      );
$form->add(
    'country', EntityType::class,
    [
        'autocomplete' => ['name', 'iso', 'phoneCode'],
        'autocomplete_related_fields' => ['continent'],
        'class' => Country::class,
        'query_builder' => function (EntityRepository $repo) {
            return $repo->createQueryBuilder('c')
                ->where('c.continent = :continent');
        },
    ]
);
````

Note the `autocomplete_related_fields` in **country** field.
Each field name given in this array should be resolved in current form and passed as parameter of the query builder. 
Then can use this name as parameter in your query builder: `:continent`. 
The name of the field and the name of the parameter used in the query should be the same.
        