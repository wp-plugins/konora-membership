<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link:       http://www.konora.com
 * @since      0.1
 *
 * @package    Konoramembership
 * @subpackage Konoramembership/includes
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Konoramembership
 * @subpackage Konoramembership/admin
 * @author:       Konora <info@konora.com>
 */
class Konoramembership_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1
	 * @access   private
	 * @var      string    $name    The ID of this plugin.
	 */
	private $name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1
	 * @var      string    $name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $version ) {

		$this->name = $name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    0.1
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Konoramembership_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Konoramembership_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->name, plugin_dir_url( __FILE__ ) . 'css/konora-membership-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    0.1
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Konoramembership_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Konoramembership_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->name, plugin_dir_url( __FILE__ ) . 'js/konora-membership-admin.js', array( 'jquery' ), $this->version, FALSE );

	}

   public function plugins_loaded() {
      $this->wpsf = new WordPressSettingsFramework(plugin_dir_path(__FILE__) . '../settings/settings.php', 'konora_membership_option');

      add_filter($this->wpsf->get_option_group() . '_settings_validate', array(&$this, 'validate_settings'));
   }

   public function add_menu() {
      add_options_page('Membership Option', 'Membership Option', 'manage_options', 'wp_konora_membership_option', array(&$this, 'plugin_settings_page'));
   }

   public function plugin_settings_page() {
      $this->wpsf->settings();
   }

   function validate_settings($input) {

      $input['privacy_cookie_law_general_url'] = esc_url($input['privacy_cookie_law_general_url'], 'http');

      return $input;
   }
}
