# CMB2-User-Select

Special CMB2 Field that allows users to define an autocomplete text field for users

### Example
```php
$cmb2->add_field( array(
	'name'  => 'Author',,
	'id'    => 'author',
	'desc'  => 'Type the name of the author and select from the options',
	'type'  => 'user_select_text'
	'options' => array(
		'minimum_user_level' => 0, // Enable search for all user levels.. use with caution.
	),
) );
```
