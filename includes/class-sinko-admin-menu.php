<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://tassawer.com
 * @since      1.0.0
 *
 * @package    Sinko_Dashboard
 * @subpackage Sinko_Dashboard/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sinko_Dashboard
 * @subpackage Sinko_Dashboard/admin
 * @author     Tassawer Hussain <support@tassawer.com>
 */
class Sinko_Admin_Menu {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// Start Admin Menu
		add_action( 'admin_menu', array( $this, 'register_main_sinko_dashboard_page' ), 9999 );
	}

	/**
	 * Register a custom menu page.
	 */
	public function register_main_sinko_dashboard_page() {

		global $submenu;
		
		add_menu_page( 
			__( 'Sinko Dashboard', 'sinko-dashboard' ),
			'Sinko Dashboard',
			'manage_options',
			'sinko-dashboard-old',
			array( $this, 'main_sinko_dashboard_page'),
			'dashicons-welcome-widgets-menus',
			2
		);

		add_submenu_page( 
			'sinko-dashboard-old',
			'Sinko Dashboard',
			'Dashboard',
			'manage_options',
			'sinko-dashboard',
			array( $this, 'main_sinko_dashboard_page'),
			'dashicons-welcome-widgets-menus',
			2
		);

		add_submenu_page( 
			'sinko-dashboard-old',
			'Clients',
			'Clients',
			'manage_options',
			'sinko-clients',
			array( $this, 'main_sinko_clients_page')
		);

		add_submenu_page( 
			'sinko-dashboard-old',
			'Staff',
			'Staff',
			'manage_options',
			'sinko-staff',
			array( $this, 'main_sinko_staff_page')
		);

		add_submenu_page( 
			'sinko-dashboard-old',
			'Export',
			'Export',
			'manage_options',
			'sinko-export',
			array( $this, 'main_sinko_export_page')
		);

		remove_submenu_page( 'sinko-dashboard-old', 'sinko-dashboard-old' );


		$sinko_menu = $submenu['sinko-dashboard-old'];
		$sinko_menu_revert = array(
			$sinko_menu[1],
			$sinko_menu[0],
			$sinko_menu[2],
			$sinko_menu[3],
			$sinko_menu[4],
		);

		$submenu['sinko-dashboard-old'] = $sinko_menu_revert;

		/*
		// Code snippet use to change the name for the submenu page different just like for the => Plugins -> Installed Plugins
		add_menu_page( 'Calculator Logs', 'Calculator Logs', 'manage_options', 'calculator-logs', array( $this, 'calculator_logs_func' ), 'dashicons-welcome-widgets-menus', 20 );
		add_submenu_page( 'calculator-logs', 'Seller Fees', 'Seller Fees', 'manage_options', 'seller-fees', array( $this, 'calculator_logs_func' ), 30 );
		remove_submenu_page( 'calculator-logs', 'calculator-logs' );
		*/

		$this->sinko_dashboard = new Sinko_Dashboard_Notifications();
		$this->sinko_clients = new Sinko_Clients();
		$this->sinko_staffs = new Sinko_Staff();
	}
	
	/**
	 * Display Main Sinko Dashboard page
	 */
	public function main_sinko_dashboard_page() {
		$this->sinko_dashboard->display_notification_list();
	}
	
	public function main_sinko_clients_page() {
		
		$this->sinko_clients->display_client_list();
		
	}

	public function main_sinko_staff_page() {
		$this->sinko_staffs->display_staff_list();
	}

	public function main_sinko_export_page() {
		echo "Hi Export";
	}

}
