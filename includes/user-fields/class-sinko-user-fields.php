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
class Sinko_User_Fields {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'user_new_form', array( $this, 'sinko_extra_user_profile_fields' ), 999, 1 );
		add_action( 'show_user_profile', array( $this, 'sinko_extra_user_profile_fields' ), 999, 1 );
		add_action( 'edit_user_profile', array( $this, 'sinko_extra_user_profile_fields' ), 999, 1 );
 
		add_action( 'user_register', array( $this, 'sinko_save_extra_user_profile_fields' ) );
		add_action( 'personal_options_update', array( $this, 'sinko_save_extra_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'sinko_save_extra_user_profile_fields' ) );

	}

	public function sinko_extra_user_profile_fields( $user ) { ?>
		<h3><?php _e("Client Fields", "blank"); ?></h3>

		<table class="form-table">
		<tr>
			<th><label for="address"><?php _e("Address"); ?></label></th>
			<td>
				<input type="text" name="client_address" id="client_address" value="<?php echo esc_attr( get_the_author_meta( 'client_address', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e("Please enter client address."); ?></span>
			</td>
		</tr>
		<tr>
			<th><label for="Contact Name"><?php _e("Contact Name"); ?></label></th>
			<td>
				<input type="text" name="client_contact_name" id="client_contact_name" value="<?php echo esc_attr( get_the_author_meta( 'client_contact_name', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e("Please enter client Contact Name."); ?></span>
			</td>
		</tr>
		<tr>
		<th><label for="VAT Number"><?php _e("VAT Number"); ?></label></th>
			<td>
				<input type="text" name="client_vat_number" id="client_vat_number" value="<?php echo esc_attr( get_the_author_meta( 'client_vat_number', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e("Please enter client VAT Number."); ?></span>
			</td>
		</tr>
		</table>
	<?php }

	
	function sinko_save_extra_user_profile_fields( $user_id ) {
		// Save/Update cleint fields.

		update_user_meta( $user_id, 'client_address', $_POST['client_address'] );
		update_user_meta( $user_id, 'client_contact_name', $_POST['client_contact_name'] );
		update_user_meta( $user_id, 'client_vat_number', $_POST['client_vat_number'] );
	}


}
