<?php
/**
 * CMB2 User Select
 *
 * Custom field for CMB2 which adds a user-search input
 *
 * @category WordPressLibrary
 * @package  WDS_CMB2_User_Select
 * @author   WebDevStudios <contact@webdevstudios.com>
 * @license  GPL-2.0+
 * @version  0.2.1
 * @link     https://github.com/WebDevStudios/CMB2-User-Select
 * @since    0.2.0
 */
class WDS_CMB2_User_Select {

	protected static $single_instance = null;
	protected static $script_added    = false;
	protected static $script_data     = array();

	/**
	 * Creates or returns an instance of this class.
	 * @since  0.2.0
	 * @return WDS_CMB2_User_Select A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Constructor (setup our hooks)
	 */
	protected function __construct() {
		add_action( 'cmb2_render_user_select_text', array( $this, 'render_user_select_field' ), 10, 5 );
		add_action( 'wp_ajax_get_users_for_user_select_field', array( $this, 'get_users_for_ajax_search' ) );
		add_action( 'wp_ajax_nopriv_get_users_for_user_select_field', array( $this, 'get_users_for_ajax_search' ) );
		add_filter( 'cmb2_sanitize_user_select_text', array( $this, 'santize_value' ), 10, 5 );
	}

	/**
	 * Render the field and setup the field for the JS autocomplete.
	 */
	public function render_user_select_field( $field, $escaped_value, $object_id, $object_type, $field_type ) {

		$value = wp_parse_args( $escaped_value, array(
			'name' => '',
			'id'   => ''
		) );

		echo $field_type->input( array(
			'id'           => $field_type->_id( '_name' ),
			'name'         => $field_type->_name( '[name]' ),
			'value'        => $value['name'],
			'autocomplete' => 'off',
		) );

		echo $field_type->input( array(
			'id'    => $field_type->_id( '_id' ),
			'name'  => $field_type->_name( '[id]' ),
			'value' => $value['id'],
			'type'  => 'hidden',
			'desc'  => ''
		) );

		self::$script_data[] = array(
			'id'    => $field->args( 'id' ),
			'roles' => implode(',', $this->get_user_roles( $field )),
		);

		if ( ! self::$script_added ) {
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			add_action( is_admin() ? 'admin_footer' : 'wp_footer', array( __CLASS__, 'footer_js' ) );
			self::$script_added = true;
		}
	}

	/**
	 * Adds JS to footer which enables the autocomplete
	 */
	public static function footer_js() {
		wp_localize_script( 'jquery-ui-autocomplete', 'cmb2_user_select_field', array(
			'field_ids' => self::$script_data,
			'ajax_url'  => admin_url( 'admin-ajax.php' ),
		) );

		?>
		<script type="text/javascript">
		jQuery(document).ready( function($) {
			var l10n = window.cmb2_user_select_field;

			if ( ! l10n.field_ids ) {
				return console.warn( 'Missing cmb2_user_select_field data!' );
			}

			var renderItem = function( ul, item ) {
				return $( '<li>' )
					.append( '<a style="cursor: pointer;">' + item.display_name + '</a>' )
					.appendTo( ul );
			};

			var setupAutocomplete = function( field_id, roles ) {
				var $field = $( document.getElementById( field_id + '_name' ) );
				if ( ! $field.length ) {
					return console.warn( 'Missing field input for ' + field_id );
				}

				$field.autocomplete({
					source : function( request, responseCallback ) {
						$.ajax({
							url      : l10n.ajax_url,
							dataType : 'json',
							data     : {
								action : 'get_users_for_user_select_field',
								roles  : roles,
								q      : request.term
							},
							success : function( response ) {
								if ( ! response.success ) {
									return console.warn( response );
								}
								responseCallback( response.data );
							}
						});
						return false;
					},
					minLength : 2,
					select : function( event, ui ) {
						$field.val( ui.item.display_name );
						$( document.getElementById( field_id + '_id' ) ).val( ui.item.ID );

						return false;
					}
				} ).autocomplete( "instance" )._renderItem = renderItem;
			};

			for ( var i = l10n.field_ids.length - 1; i >= 0; i-- ) {
				setupAutocomplete( l10n.field_ids[i].id, l10n.field_ids[i].roles );
			}

		});
		</script>
		<?php
	}

	/**
	 * Get field min. user level (defaults to 2).
	 *
	 * @param  CMB2_Field $field
	 *
	 * @return int Numeric min. user level.
	 */
	public function get_user_roles( CMB2_Field $field ) {
		$roles = $field->options( 'user_roles' );

		return is_array($roles) ? $roles : array();
	}

	/**
	 * Santize/validate the user search field value
	 */
	function santize_value( $override_value, $value, $object_id, $args, $sanitizer ) {
		// Clean up
		$value = array_map( 'sanitize_text_field', $value );

		// No name, clear the value.
		if ( empty( $value['name'] ) ) {
			$value = '';
		}

		// If we have a name, do some additional validation
		if ( ! empty( $value['name'] ) ) {

			// Get field min. user level
			$roles = $this->get_user_roles( $sanitizer->field );

			// Check if name matches a user search
			$users = $this->users_search_by_role( $value['name'], $roles );

			// If not, clear the value.
			if ( empty( $users ) ) {
				$value = '';
			} else {

				$unset = true;

				// Loop the found users, and check if the name matches
				foreach ( $users as $user ) {
					if ( $user->display_name == $value['name'] ) {
						$unset = false;
						break;
					}
				}

				// If no matches, clear the value.
				if ( $unset ) {
					$value = '';
				}
			}
		}

		// Return the sanitized/validated value.
		return $value;
	}

	/**
	 * Gets the users based on the search string/user-level provided via ajax
	 */
	public function get_users_for_ajax_search() {
		$search_query = isset( $_REQUEST['q'] ) ? $_REQUEST['q'] : '';
		$roles = !empty( $_REQUEST['roles'] )
			? explode(',', sanitize_text_field($_REQUEST['roles']) )
			: array();

		if ( empty( $search_query ) ) {
			wp_send_json_error( 'No search query' );
		}


		$users = $this->users_search_by_role( $search_query, $roles );
		wp_send_json_success( $users );
	}

	/**
	 * Gets the users based on the search string, and the user level provided.
	 * @todo wp_user_level is way-deprecated. Use capabilities instead.
	 */
	public function users_search_by_role( $search_query, $roles = array() ) {
		if ( empty( $search_query ) ) {
			return false;
		}

		$user_args = array(
			'search' 		=> sanitize_text_field( $search_query . '*' ),
			'fields' 		=> array( 'display_name', 'ID' )
		);
		if(!empty($roles)){
			$user_args['role__in'] = $roles;
		}

		$user_args = apply_filters( 'wds_cmb2_user_select_search_args', $user_args, $this );
		return get_users( $user_args );
	}

}
WDS_CMB2_User_Select::get_instance();
