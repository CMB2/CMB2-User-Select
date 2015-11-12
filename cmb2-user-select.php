<?php
/*
  Plugin Name: WDS CMB2 User Select
  Plugin URI: http://webdevstudios.com
  Description: Custom field for CMB2 which adds a user-search input
  Author: WebDevstudios
  Author URI: http://webdevstudios.com
  Version: 0.1.0
  License: GPLv2
 */

function cmb2_render_user_select_field( $field, $escaped_value, $object_id, $object_type, $field_type ) {

	$value = wp_parse_args( $escaped_value, array(
		'name' => '',
		'id' => ''
	) );
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-autocomplete' );

	$field_args = array(
		'field_id' => $field->args('id'),
		'ajax_url' => admin_url('admin-ajax.php')
	);

	wp_localize_script( 'jquery-ui-autocomplete', 'cmb2_user_select_field', $field_args );

	echo $field_type->input( array(
		'id' 	=> $field->args('id') . '_name',
		'name' 	=> $field->args('id') . '[name]',
		'value'	=> $value['name'],
	    'autocomplete' => 'off',
	) );

	echo $field_type->input( array(
		'id' 	=> $field->args('id') . '_id',
		'name'	=> $field->args('id') . '[id]',
	    'value'	=> $value['id'],
	    'type' 	=> 'hidden',
	    'desc'	=> ''
	) );

	?>

	<script>
	  jQuery(document).ready( function($) {
	    
	    $( "#" + cmb2_user_select_field.field_id + '_name' ).autocomplete({
	      	source: function( request, response ) {
	      		
	      		$.ajax({
					url: cmb2_user_select_field.ajax_url,
					dataType: "json",
					data: {
						action: 'get_users_for_user_select_field',
						q: request.term
					},
					success: function( data ) {
						response( data );
					}
				});
				
				return false;

	      	},
	      	minLength: 2,
	      	select: function( event, ui ) {
	      		
	      		$( '#' + cmb2_user_select_field.field_id + '_name' ).val( ui.item.display_name );
	      		$( '#' + cmb2_user_select_field.field_id + '_id' ).val( ui.item.ID );
	      		
	      		return false;

	      	}
	    }).autocomplete( "instance" )._renderItem = function( ul, item ) {

	      	return $( "<li>" )
	        	.append( "<a style=\"cursor: pointer;\">" + item.display_name + "</a>" )
	        	.appendTo( ul );

	    };

	  });
	  </script>

	<?php

	return false;

}

add_action( 'cmb2_render_user_select_text', 'cmb2_render_user_select_field', 10, 5 );

/**
 * Gets the users based on the search string provided
 * @return [type] [description]
 */
function get_users_for_user_select_field() {

	$search_query = isset( $_REQUEST['q'] ) ? $_REQUEST['q'] : '';

	if ( empty( $search_query ) ) {
		return false;
	}

	$user_args = array(
		'search' 		=> sanitize_text_field( $search_query . '*' ),
		'fields' 		=> array( 'display_name', 'ID' ),
		'meta_query' 	=> array(
			array( 
				'key' => 'wp_user_level',
				'value' => '2',
				'compare' => '>=',
				'type' => 'NUMERIC'
			)
		)
	);

	$users = get_users( $user_args );

	echo json_encode( $users );
	exit;

}

add_action( 'wp_ajax_get_users_for_user_select_field', 'get_users_for_user_select_field' );
add_action( 'wp_ajax_nopriv_get_users_for_user_select_field', 'get_users_for_user_select_field' );