# CMB2-User-Select

Special CMB2 Field that allows users to define an autocomplete text field for users

### Example
<pre>
$cmb2->add_field( array(
  'name'  => __( 'Author', 'wds-new-wiki-post-form' ),
  'id'    => 'author',
  'desc'  => __( 'Type the name of the strategist and select from the dropdown', 'cmb2' ),
  'type'  => 'user_select_text'
) );
</pre>
