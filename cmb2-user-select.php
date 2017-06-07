<?php
/**
 * Plugin Name: WDS CMB2 User Select
 * Plugin URI: http://webdevstudios.com
 * Description: Custom field for CMB2 which adds a user-search input
 * Author: WebDevstudios
 * Author URI: http://webdevstudios.com
 * Version: 0.2.3
 * License: GPLv2
 */

/**
 * WDS_CMB2_User_Select loader
 *
 * Handles checking for and smartly loading the newest version of this library.
 *
 * @category  WordPressLibrary
 * @package   WDS_CMB2_User_Select
 * @author    WebDevStudios <contact@webdevstudios.com>
 * @copyright 2016 WebDevStudios <contact@webdevstudios.com>
 * @license   GPL-2.0+
 * @version   0.2.3
 * @link      https://github.com/WebDevStudios/CMB2-User-Select
 * @since     0.2.1
 */

/**
 * Copyright (c) 2016 WebDevStudios (email : contact@webdevstudios.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Loader versioning: http://jtsternberg.github.io/wp-lib-loader/
 */

if ( ! class_exists( 'WDS_CMB2_User_Select_023', false ) ) {

	/**
	 * Versioned loader class-name
	 *
	 * This ensures each version is loaded/checked.
	 *
	 * @category WordPressLibrary
	 * @package  WDS_CMB2_User_Select
	 * @author   WebDevStudios <contact@webdevstudios.com>
	 * @license  GPL-2.0+
	 * @version  0.2.2
	 * @link     https://github.com/WebDevStudios/CMB2-User-Select
	 * @since    0.2.1
	 */
	class WDS_CMB2_User_Select_023 {

		/**
		 * WDS_CMB2_User_Select version number
		 * @var   string
		 * @since 0.2.1
		 */
		const VERSION = '0.2.3';

		/**
		 * Current version hook priority.
		 * Will decrement with each release
		 *
		 * @var   int
		 * @since 0.2.1
		 */
		const PRIORITY = 9997;

		/**
		 * Starts the version checking process.
		 * Creates WDS_CMB2_USER_SELECT_LOADED definition for early detection by
		 * other scripts.
		 *
		 * Hooks WDS_CMB2_User_Select inclusion to the wds_cmb2_user_select_load hook
		 * on a high priority which decrements (increasing the priority) with
		 * each version release.
		 *
		 * @since 0.2.1
		 */
		public function __construct() {
			if ( ! defined( 'WDS_CMB2_USER_SELECT_LOADED' ) ) {
				/**
				 * A constant you can use to check if WDS_CMB2_User_Select is loaded
				 * for your plugins/themes with WDS_CMB2_User_Select dependency.
				 *
				 * Can also be used to determine the priority of the hook
				 * in use for the currently loaded version.
				 */
				define( 'WDS_CMB2_USER_SELECT_LOADED', self::PRIORITY );
			}

			// Use the hook system to ensure only the newest version is loaded.
			add_action( 'wds_cmb2_user_select_load', array( $this, 'include_lib' ), self::PRIORITY );

			// Use the hook system to ensure only the newest version is loaded.
			add_action( 'after_setup_theme', array( $this, 'do_hook' ) );
		}

		/**
		 * Fires the cmb2_attached_posts_field_load action hook
		 * (from the after_setup_theme hook).
		 *
		 * @since 1.2.3
		 */
		public function do_hook() {
			// Then fire our hook.
			do_action( 'wds_cmb2_user_select_load' );
		}

		/**
		 * A final check if WDS_CMB2_User_Select exists before kicking off
		 * our WDS_CMB2_User_Select loading.
		 *
		 * WDS_CMB2_USER_SELECT_VERSION and WDS_CMB2_USER_SELECT_DIR constants are
		 * set at this point.
		 *
		 * @since  0.2.1
		 */
		public function include_lib() {
			if ( class_exists( 'WDS_CMB2_User_Select', false ) ) {
				return;
			}

			if ( ! defined( 'WDS_CMB2_USER_SELECT_VERSION' ) ) {
				/**
				 * Defines the currently loaded version of WDS_CMB2_User_Select.
				 */
				define( 'WDS_CMB2_USER_SELECT_VERSION', self::VERSION );
			}

			if ( ! defined( 'WDS_CMB2_USER_SELECT_DIR' ) ) {
				/**
				 * Defines the directory of the currently loaded version of WDS_CMB2_User_Select.
				 */
				define( 'WDS_CMB2_USER_SELECT_DIR', dirname( __FILE__ ) . '/' );
			}

			// Include and initiate WDS_CMB2_User_Select.
			require_once WDS_CMB2_USER_SELECT_DIR . 'lib/init.php';
		}

	}

	// Kick it off.
	new WDS_CMB2_User_Select_023;
}
