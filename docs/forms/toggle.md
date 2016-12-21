## Toggle Extension

This extension makes it easy to show and hide elements based on the state of a checkbox, radio button, or select menu.

### Options:

### Usage:

#### Basic usage

````php
$form->add(
    'enabled', CheckboxType::class,
    [
        'toggle' => 'enable_user'
    ]
);

$form->add('Name', TextType::class, ['toggle_group' => 'enable_user']);
````

In the above example the field `name` only is visible if the field `enabled` is checked. 
The field `enabled` has the option `toggle` with the name of group to show or hide. 
In the other hand the field name has the name of the groups that belongs.

The toggle works with selects too, in this case use the option `toggle_prefix`

````php
$form->add(
    'Device', ChoiceType::class,
    [
        'choices' =>
            [
                '' => '',
                'Mobile' => 'mobile',
                'Tablet' => 'tablet',
                'Pc' => 'pc',
            ],
        'toggle_prefix' => 'device_type_',
    ]
);

$form->add(
    'mobile_color', ColorPickerType::class,
    [
        'toggle_group' => ['device_type_mobile'],
    ]
);
````

In the above example the `mobile_color` field only appear if device type selected is a mobile.

> NOTE: the `mobile_color` field use the prefix used followed by the input option as a group

Can show the input for multiple options, 

````php
$form->add(
    'mobile_color', ColorPickerType::class,
    [
        'toggle_group' => ['device_type_mobile','device_type_tablet'],
    ]
);
````

#### Reverse

In the other hand can do the reverse effect using the `not_` prefix before the group name.

````php
$form->add(
    'mobile_color', ColorPickerType::class,
    [
        'toggle_group' => ['not_device_type_pc'],
    ]
);
````

The above example only show this field if the selected option is not a pc device.

The reverse works with checkbox too:

````php
$form->add('Name', TextType::class, ['toggle_group' => 'not_enable_user']);
````


#### Use with htm content

If you need use this behavior with custom html content.
For example; to show some part of the layout based on the selected elements in the form, 
the only that you need is add the respective css class.

````php
$form->add(
    'enabled', CheckboxType::class,
    [
        'toggle' => 'enable_user'
    ]
);
````

````html
<div class="toggle_group_enabled">
This user is enabled!
</div>
````

or 

````html
<div class="toggle_group_not_enabled">
This user is NOT enabled!
</div>
````

toggle_group_(not_){group_name_or_prefix}{option_name}

- `toggle_group_` -> Permanent prefix to avoid conflicts with other classes
- `not_` -> (optional) To add reverse behavior
- `{group_name_or_prefix}` -> The name of the group or prefix in case of select
- `{option_name}` -> Name of the option only if is a select

##### Examples

- toggle_group_enabled
- toggle_group_not_enabled
- toggle_group_device_type_pc
- toggle_group_not_device_type_pc

#### Chained groups

You can chain form inputs to show or hide elements based on previous selected options.

````php
$form->add(
    'enabled', CheckboxType::class,
    [
        'toggle' => 'enabled',
    ]
);

$form->add(
    'Device', ChoiceType::class,
    [
        'choices' =>
            [
                '' => '',
                'Mobile' => 'mobile',
                'Pc' => 'pc',
            ],
        'toggle_prefix' => 'device_type_',
        'toggle_group' => 'enabled',
    ]
);

$form->add(
    'mobile_color', ColorPickerType::class,
    [
        'toggle_group' => ['enabled', 'device_type_mobile'],
    ]
);

$form->add(
    'pc_type', ChoiceType::class,
    [
        'choices' =>
            [
                'Laptop' => 'laptop',
                'Desktop' => 'desktop',
            ],
        'toggle_group' => ['enabled', 'device_type_pc'],
    ]
);
````

> NOTE: The `pc_type` input has two groups `['enabled', 'device_type_pc']` this is required to only show this 
input if the `enabled` option is checked and the `Device` is a PC.
