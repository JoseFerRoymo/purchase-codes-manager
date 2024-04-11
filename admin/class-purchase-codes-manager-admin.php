<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://roymo.es/
 * @since      1.0.0
 *
 * @package    Purchase_Codes_Manager
 * @subpackage Purchase_Codes_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Purchase_Codes_Manager
 * @subpackage Purchase_Codes_Manager/admin
 * @author     Rommel & Montgomery <dev@roymo.es>
 */
class Purchase_Codes_Manager_Admin {

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
		 * defined in Purchase_Codes_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Purchase_Codes_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/purchase-codes-manager-admin.css', array(), $this->version, 'all' );

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
		 * defined in Purchase_Codes_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Purchase_Codes_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/purchase-codes-manager-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function add_promotional_codes_manager(){
		add_menu_page(
			'Códigos promocionales',
			'Códigos promocionales',
			'read',
			'codigos-promocionales',
			[$this, 'promotional_codes_page'],
			'',
			6
		);
	}

	public function add_admin_table_purchases(){
		add_menu_page(
			'Administrar códigos',
			'Administrar códigos',
			'manage_options',
			'administrar-codigos',
			[$this, 'code_manager'],
			'',
			8
		);
	}

	public function promotional_codes_page(){
		include_once plugin_dir_path(__FILE__) . 'partials/purchase-codes-manager-admin-display.php';
	}
	public function code_manager(){
		include_once plugin_dir_path(__FILE__) . 'partials/purchase-codes-manager-admin-table-view.php';
	}
}
