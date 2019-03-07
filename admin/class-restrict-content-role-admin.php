<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://simple-code.hr
 * @since      1.0.0
 *
 * @package    Restrict_Content_Role
 * @subpackage Restrict_Content_Role/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Restrict_Content_Role
 * @subpackage Restrict_Content_Role/admin
 * @author     Simple Code d.o.o. <info@simple-code.hr>
 */
class Restrict_Content_Role_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Current user object
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $user    Logged in user details.
	 */
	private $user;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Restrict_Content_Role_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Restrict_Content_Role_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/restrict-content-role-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Restrict_Content_Role_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Restrict_Content_Role_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/restrict-content-role-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Filter admin page results based on user role
	 */
	public function filter_admin_pages( $query ) {

		$screen = get_current_screen();

		if (
			!$query->is_main_query() ||
			!is_user_logged_in() ||
			!($screen && is_object($screen) && $screen->post_type === 'page')
		) {
			return;
		}

		$visible_ids = $this->get_role_visible_pages();

		if (count($visible_ids) > 0) {
			$query->set('post__in', $visible_ids);
		}

		return $query;

	}

	public function get_role_visible_pages() {

		$this->user = wp_get_current_user();
		$roles_settings = $this->get_roles_settings();
		$visible_ids = array();

		foreach ( $this->user->roles as $role ) {

			$key_visibility = 'visibility';
			$key_pages = 'pages';

			if (
				array_key_exists( $role, $roles_settings ) &&
				array_key_exists( $key_visibility, $roles_settings[$role] ) &&
				$roles_settings[$role][$key_visibility] === 'custom'
			) {
				$visible_ids = array_merge($visible_ids, $roles_settings[$role][$key_pages]);
			}

		}

		return $visible_ids;

	}

	/**
	 * Get an array of chosen settings for all roles
	 */
	public function get_roles_settings() {

		global $sc_restrict_content;
		$roles_settings = array();

		foreach ($sc_restrict_content as $key => $value) {

			if (strpos($key, $this->plugin_name ) !== false) {
				$role_field_array = array_values(array_filter(explode($this->plugin_name . '-', $key)));
				$role_field_array = explode('-', $role_field_array[0]);

				if (is_array($role_field_array) && count($role_field_array) == 2) {
					$role = $role_field_array[0];
					$field = $role_field_array[1];

					if (!array_key_exists($role, $roles_settings)) {
						$roles_settings[$role] = array();
					}

					$roles_settings[$role][$field] = $value;
				}
			}

		}

		return $roles_settings;

	}

	/**
	 * Alter view count (all, published)
	 */
	public function alter_view_count( $views ) {

		$visible_ids = $this->get_role_visible_pages();

		if (count($visible_ids) > 0) {

			$args = array(
				'post_type' => 'page',
				'post__in' => $visible_ids
			);
			$pages_info = new WP_Query( $args );

			$pages_status = [];
			$pages_status_total = 0;

			while ($pages_info -> have_posts()) {
				$pages_info -> the_post();
				$page_status = get_post_status();
				$pages_status_total++;

				if (!array_key_exists($page_status, $pages_status)) {
					$pages_status[$page_status] = 0;
				}

				$pages_status[$page_status]++;
			}

			foreach ($pages_status as $key => $count) {
				if (array_key_exists($key, $views)) {
					$views[$key] = $this->replace_view_count( $count, $views[$key] );
				}

			}

			if (array_key_exists('all', $views)) {
				$views['all'] = $this->replace_view_count( $pages_status_total, $views['all'] );
			}

			foreach ($views as $key => $view) {
				if ($key !== 'all' && !array_key_exists($key, $pages_status)) {
					unset($views[$key]);
				}
			}

		}

		return $views;

	}

	/**
	 * Replace the count of a view
	 */
	public function replace_view_count( $count, $view ) {

		return preg_replace('/(<span.*?>).*?(<\/span>)/', '$1 ('.$count.') $2', $view);

	}

}
