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

if ( !class_exists('WP_List_Table') ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Sinko_Clients extends WP_List_Table {

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ) {
        
        $this->compat_fields['add_nonce'] = wp_create_nonce( 'sinko_add_client' );
        $this->compat_fields['edit_nonce'] = wp_create_nonce( 'sinko_edit_client' );
        $this->compat_fields['delete_nonce'] = wp_create_nonce( 'sinko_delete_client' );

        parent::__construct( [
			'singular' => __( 'Client', 'sinko-dashboard' ), //singular name of the listed records
			'plural'   => __( 'Clients', 'sinko-dashboard' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
		] );

	}

    /**
     * Retrieve assets’s data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_client( $per_page = 20, $page_number = 1 ) {

        $sinko_role = serialize(array( 'sinko_client' => true ));

        global $wpdb;

        $sql = stripslashes( $wpdb->prepare("SELECT {$wpdb->users}.ID FROM {$wpdb->users}
        INNER JOIN {$wpdb->usermeta} ON {$wpdb->users}.ID= {$wpdb->usermeta}.user_id
        WHERE {$wpdb->usermeta}.meta_key='wp_capabilities' 
        AND {$wpdb->usermeta}.meta_value=%s", $sinko_role) );
    
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
        } else {
			$sql .= ' ORDER BY id DESC';
		}
    
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
    
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
    
        return $result;
    }

    /**
     * Delete an asset record.
     *
     * @param int $id asset ID
     */
    public static function delete_notification( $id ) {
        
        // use wp_delete_user() function in a plugin then we must include
        require_once ABSPATH . 'wp-admin/includes/user.php';
        
        wp_delete_user( $id );
    }

    /**
     * Returns the count of assets in the database.
     *
     * @return null|string
     */
    public static function record_count() {

        $sinko_role = serialize(array( 'sinko_client' => true ));

        global $wpdb;

        $sql = stripslashes( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->users}
        INNER JOIN {$wpdb->usermeta} ON {$wpdb->users}.ID= {$wpdb->usermeta}.user_id
        WHERE {$wpdb->usermeta}.meta_key='wp_capabilities' 
        AND {$wpdb->usermeta}.meta_value=%s", $sinko_role) );
    
        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No client avaliable.', 'sinko-dashboard' );
    }

    /**
     * Associative array of columns
     * List of column that appear as a table head
     *
     * @return array
     */
    public function get_columns() {
        $columns = [
            'cb'       => '<input type="checkbox" />',
            'clients'  => __( 'Clients', 'sinko-dashboard' ),
            'projects' => __( 'Projects', 'sinko-dashboard' ),
            'staffs'   => __( 'Staffs', 'sinko-dashboard' ),
        ];
    
        return $columns;
    }

    /**
     * Render a column value when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case '':
                return $item[ $column_name ];
        default:
            return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
        '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_clients( $item ) {

        $staff_obj = get_user_by( 'id', $item['ID'] );
		$client_name = $staff_obj->first_name . ' ' . $staff_obj->last_name;

		$title = $client_name;

        $actions = [
            'Edit' => sprintf( '<a href="?page=%s&action=%s&client=%s&_wpnonce=%s">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit-client', absint( $item['ID'] ), $this->compat_fields['edit_nonce'] ),
            'delete' => sprintf( '<a href="?page=%s&action=%s&client=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete-client', absint( $item['ID'] ), $this->compat_fields['delete_nonce'] )
        ];

        return $title . $this->row_actions( $actions );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_projects( $item ) {

        $client_projects = get_user_meta( $item['ID'], 'projects_assigend' );

        if( empty( $client_projects ) ) {
            return 'No project assigned yet.';
        }

        $title = sprintf( '<a href="?page=%s&action=%s&client=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'edit-client', absint( $item['ID'] ), count( maybe_unserialize( $client_projects ) ), $this->compat_fields['edit_nonce'] );

        return $title;
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_staffs( $item ) {

        $client_staff = get_user_meta( $item['ID'], 'staff_assigend' );

        if( empty( $client_staff ) ) {
            return 'No staff assigned yet.';
        }

        $title = sprintf( '<a href="?page=%s&action=%s&client=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'edit-client', absint( $item['ID'] ), count( maybe_unserialize( $client_projects ) ), $this->compat_fields['edit_nonce'] );

        return $title;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'clients' => array( 'clients', true ),
        );
    
        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
        	'bulk-delete' => 'Delete'
        ];
    
        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        // $this->_column_headers = $this->get_column_info();
    
        /** Process bulk action */
        $this->process_bulk_action();
    
        $per_page     = $this->get_items_per_page( 'clients_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();
    
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );
    
        $this->items = self::get_client( $per_page, $current_page );
		
    }

    /**
     * Takes care of the deleting customers record either when the delete link is clicked or
     * when a group of records is checked and the delete option is selected from the bulk action
     */
    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete-client' === $this->current_action() ) {
      
          	// In our file that handles the request, verify the nonce.
          	$nonce = esc_attr( $_REQUEST['_wpnonce'] );
      
          	if ( ! wp_verify_nonce( $nonce, 'sinko_delete_client' ) ) {
            	die( 'Go get a life script kiddies' );
          	} else {
            	self::delete_notification( absint( $_GET['client'] ) );
      
            	wp_safe_redirect( esc_url( add_query_arg(
                    array( 
                        'message' => 'Client deleted' ),
                        admin_url('/sinko-clients') 
                    ) )
                );
            	exit;
          	}
        }
      
        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
             || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {
      
          	$delete_ids = esc_sql( $_POST['bulk-delete'] );
      
          	// loop over the array of record IDs and delete them
          	foreach ( $delete_ids as $id ) {
            	self::delete_notification( $id );
          	}
      
          	wp_safe_redirect( esc_url( add_query_arg(array( 
                    'message' => 'Client deleted' ),
                    admin_url('/sinko-clients') 
                ) )
            );
         	exit;
        }
    }


    public function display_client_list() { ?>
        <div class="wrap">
            <h2>
                <?php echo __('Clients', 'sinko-dashboard'); ?>
                <a class="add-new-h2 sinko-create-client" 
                    href="<?php echo add_query_arg( array( 
                        'page' => 'sinko-clients' ,
                        'action' => 'create-client' ,
                        '_wpnonce' => $this->compat_fields['add_nonce'] ),
                        admin_url('/admin.php') ); ?>">
                Create Client</a>
            </h2>

            <?php
            if( isset( $_GET['message'] ) && !empty( $_GET['message'] )) { ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( $_GET['message'], 'sample-text-domain' ); ?></p>
    </div>
                <?php
            }
            ?>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-1">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php $this->prepare_items();
        							$this->display(); ?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
    <?php }

}

