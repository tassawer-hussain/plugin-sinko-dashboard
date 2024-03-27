<?php

/**
 * Fired during plugin activation
 *
 * @link       https://tassawer.com
 * @since      1.0.0
 *
 * @package    Sinko_Dashboard
 * @subpackage Sinko_Dashboard/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sinko_Dashboard
 * @subpackage Sinko_Dashboard/includes
 * @author     Tassawer Hussain <support@tassawer.com>
 */
class Sinko_Dashboard_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// add sinko-staff role.
		add_role( 'sinko_staff', 'Staff', array( 'read' => false, 'level_0' => true ) );
		
		// add sinko-client role.
		add_role( 'sinko_client', 'Client', array( 'read' => false, 'level_0' => true ) );

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
   		
		$table_sinko_notifications = $wpdb->prefix . "sinko_notifications";
		$sql = "CREATE TABLE $table_sinko_notifications (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			staff_id mediumint(9) NOT NULL,
			project_id mediumint(9) NOT NULL,
			notification text NOT NULL,
			notification_date datetime NOT NULL,
			reading_status varchar(55) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		dbDelta( $sql );
	}

}
