<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://simple-code.hr
 * @since      1.0.0
 *
 * @package    Restrict_Content_Role
 * @subpackage Restrict_Content_Role/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Restrict_Content_Role
 * @subpackage Restrict_Content_Role/includes
 * @author     Simple Code d.o.o. <info@simple-code.hr>
 */
class Restrict_Content_Role_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'restrict-content-role',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
