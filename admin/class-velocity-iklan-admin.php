<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://velocitydeveloper.com
 * @since      1.0.0
 *
 * @package    Velocity_Iklan
 * @subpackage Velocity_Iklan/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Velocity_Iklan
 * @subpackage Velocity_Iklan/admin
 * @author     Velocity Developer <bantuanvelocity@gmail.com>
 */
class Velocity_Iklan_Admin {

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
		 * defined in Velocity_Iklan_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Velocity_Iklan_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$page = isset($_GET['page']) ? $_GET['page'] : '';

		if ($page == 'iklan_dashboard') {
			
			// memanggil css dari theme utama
		   if (file_exists(get_template_directory() . '/css/theme.min.css')) {            
			   $the_theme     	= wp_get_theme();
			   $theme_version 	= $the_theme->get( 'Version' );
			   wp_enqueue_style( 'justg-styles', get_template_directory_uri() . '/css/theme.min.css', array(), $theme_version );
		   }

		   wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/velocity-iklan-admin.css', array(), $this->version, 'all' );
		}

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
		 * defined in Velocity_Iklan_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Velocity_Iklan_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		 $page = isset($_GET['page']) ? $_GET['page'] : '';

		 if ($page == 'iklan_dashboard') {
			 
			// memanggil js dari theme utama
			if (file_exists(get_template_directory() . '/js/theme.min.js')) {            
				$the_theme     	= wp_get_theme();
				$theme_version 	= $the_theme->get( 'Version' );
				wp_enqueue_script( 'justg-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $theme_version, true );
			}

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/velocity-iklan-admin.js', array( 'jquery' ), $this->version, false );
			wp_localize_script(
				$this->plugin_name,
				'velocityiklan',
				array(
					'ajaxurl' => admin_url('admin-ajax.php'),
				)
			);
		 }

	}

}
