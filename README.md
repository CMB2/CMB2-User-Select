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
		'user_roles' => array( 'role1', 'role2' ), // Specify which roles to query for.
	),
) );
```
