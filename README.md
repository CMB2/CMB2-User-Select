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

The value returned is an array with the users `id` and `name`, eg:

```php
array(
    'id' => 1
    'name' => 'Joe Blogs'
)
```

This will be serialised if saved directly into the database.

If you wish to store the users ID only, you can use `escape_cb` and `sanitization_cb` to transform
the data.

```php
/**
 * Takes the id from the database and returns an array for user_select_text
 * @param int $value
 * @return array('name' => string, 'id' => int)
 */
function id_to_user_select_text($value) {
    $user = get_user_by('id', (int)$value);
    return array(
        'name' => $user->display_name,
        'id' => $user->ID,
    );
}

/**
 * Takes the array from user_select_text and returns the id for the database
 * @param array('name' => string, 'id' => int) $value
 * @return int
 */
function user_select_text_to_id($value) {
    return $value['id'];
}

$cmb2->add_field( array(
    // ...snip...
    'escape_cb' => 'user_select_text_to_id',
    'sanitization_cb' => 'id_to_user_select_text',
) );
```
