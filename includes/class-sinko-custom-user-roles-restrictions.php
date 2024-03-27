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
class Sinko_Custom_User_Role_Restrictions {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ) {

		add_action( 'admin_menu', array( $this, 'remove_dashboard_for_cutom_roles' ), 9999 ); // Remove Dashboard page.
		add_action( 'admin_init', array( $this, 'disallowed_admin_dashboard_page' ) ); // redirect to profile page on visiting dashboard.

		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_logo_wp_admin' ) ); // Remove WP Logo.
		add_filter( 'login_redirect', array( $this, 'sinko_custom_login_redirect' ), 999, 3 ); // Redirect to profile page if sinko staff or client.

	}

	public function remove_dashboard_for_cutom_roles() { 
		// global $submenu;

		// echo "<pre>";
		// print_r($submenu);
		// echo "</pre>";
		// die();
		if ( $this->is_sinko_staff_or_client() ) {
			remove_menu_page( 'index.php' ); // Removes 'Dashboard'.
		}
	}

	public function is_sinko_staff_or_client() {
		$user = wp_get_current_user();
    	$roles = ( array ) $user->roles;

		// user has more than one role.
		if ( count( $roles ) > 1 ) {
			return false;
		}

		if ( in_array( "sinko_staff", $roles ) || in_array( "sinko_client", $roles ) ) {
			return true;
		}

		return false;

	}

	public function disallowed_admin_dashboard_page() {
		if ( $this->is_sinko_staff_or_client() ) {
			global $pagenow;
	
			// Check current admin page.
			if ( $pagenow == 'index.php' ) {
				wp_redirect( admin_url( '/profile.php' ) );
				exit;
			}
		}
	}

	public function remove_logo_wp_admin() {

		if ( $this->is_sinko_staff_or_client() ) {
			global $wp_admin_bar;
			$wp_admin_bar->remove_menu( 'wp-logo' );
		}
	}

	public function sinko_custom_login_redirect( $redirect_to, $request, $user ) {

		if( $request !==  admin_url() ) {
			return  $redirect_to;
		} 
	
		if ( is_array( $user->roles ) && count( $user->roles ) > 1 ) {
			return  $redirect_to;
		}

		$user_role = $user->roles[0]; // Get the current user's role.
		
		// Set the URL to redirect users to based on their role
		if ( $user_role == 'sinko_staff' ) {
			$redirect_to = admin_url( '/profile.php' );
		} elseif ( $user_role == 'sinko_client' ) {
			$redirect_to = admin_url( '/profile.php' );
		}
		
		return $redirect_to;
	}
}
