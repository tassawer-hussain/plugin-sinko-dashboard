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

class Sinko_Dashboard_Notifications extends WP_List_Table {

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ) {

		parent::__construct( [
			'singular' => __( 'Notification', 'sinko-dashboard' ), //singular name of the listed records
			'plural'   => __( 'Notifications', 'sinko-dashboard' ), //plural name of the listed records
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
    public static function get_notifications( $per_page = 20, $page_number = 1 ) {

        global $wpdb;
    
        $sql = "SELECT * FROM {$wpdb->prefix}sinko_notifications";
    
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
        global $wpdb;
    
        $wpdb->delete(
            "{$wpdb->prefix}sinko_notifications",
            [ 'id' => $id ],
            [ '%d' ]
        );
    }

    /**
     * Returns the count of assets in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;
    
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}sinko_notifications";
    
        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No notifications avaliable.', 'sinko-dashboard' );
    }

    /**
     * Associative array of columns
     * List of column that appear as a table head
     *
     * @return array
     */
    public function get_columns() {
        $columns = [
        'cb'                    => '<input type="checkbox" />',
        'notification'          => __( 'Notification', 'sinko-dashboard' ),
        'notification_date'     => __( 'Date', 'sinko-dashboard' ),
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
            case 'notification_date':
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
        '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_notification( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'sinko_delete_notification' );

		$staff_obj = get_user_by( 'id', $item['staff_id'] );
		$staff_name = $staff_obj->first_name . ' ' . $staff_obj->last_name;

		$project_title = get_the_title( $item['project_id'] );
    
        $title = $staff_name . ' ' . $item['notification'] . ' on project named ' . $project_title;

		// mean didn't read it yet.
		if ( 'unread' == $item['reading_status'] ) {
			$title = '<strong>' . $title . '</strong>';
		}
    
        $actions = [
            'delete' => sprintf( '<a href="?page=%s&action=%s&notification=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        ];

        return $title . $this->row_actions( $actions );
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'notification_date' => array( 'notification_date', true ),
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
    
        $per_page     = $this->get_items_per_page( 'notifications_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();
    
        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );
    
        $this->items = self::get_notifications( $per_page, $current_page );
		
    }

    /**
     * Takes care of the deleting customers record either when the delete link is clicked or
     * when a group of records is checked and the delete option is selected from the bulk action
     */
    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
      
          	// In our file that handles the request, verify the nonce.
          	$nonce = esc_attr( $_REQUEST['_wpnonce'] );
      
          	if ( ! wp_verify_nonce( $nonce, 'sinko_delete_notification' ) ) {
            	die( 'Go get a life script kiddies' );
          	} else {
            	self::delete_notification( absint( $_GET['notification'] ) );
      
            	wp_redirect( esc_url( add_query_arg() ) );
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
      
          	wp_redirect( esc_url( add_query_arg() ) );
         	exit;
        }
    }


    public function display_notification_list() { ?>
        <div class="wrap">
            <h2>
                <?php echo __('Welcome to the dashboard', 'sinko-dashboard'); ?>
            </h2>
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