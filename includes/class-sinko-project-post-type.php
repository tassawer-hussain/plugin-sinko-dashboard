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
class Sinko_Project_Post_Type {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action('init', array( $this, 'sinko_custom_post_type_projects' ) );

	}

	public function sinko_custom_post_type_projects() {
		$args = array(
			'singular' => 'Project',
			'plural' => 'Projects',
			'key' => 'sinko-project',
			'is_public' => false,
			'text_domain' => 'sinko-dashboard',
			'icon' => 'dashicons-images-alt2',
		);

		$this->general_post_type_code( $args );

	}

	public function general_post_type_code($args_rec) {
		$labels = array(
			'name' => _x($args_rec['plural'], 'Post Type General Name', 'sinko-dashboard'),
			'singular_name' => _x($args_rec['singular'], 'Post Type Singular Name', 'sinko-dashboard'),
			'menu_name' => __($args_rec['plural'], 'sinko-dashboard'),
			'name_admin_bar' => __($args_rec['singular'], 'sinko-dashboard'),
			'archives' => __($args_rec['singular'] . ' Archives', 'sinko-dashboard'),
			'attributes' => __($args_rec['singular'] . ' Attributes', 'sinko-dashboard'),
			'parent_item_colon' => __('Parent ' . $args_rec['singular'] . ':', 'sinko-dashboard'),
			'all_items' => __( $args_rec['plural'], 'sinko-dashboard'),
			'add_new_item' => __('Add New ' . $args_rec['singular'], 'sinko-dashboard'),
			'add_new' => __('Add New ' . $args_rec['singular'], 'sinko-dashboard'),
			'new_item' => __('New ' . $args_rec['singular'], 'sinko-dashboard'),
			'edit_item' => __('Edit ' . $args_rec['singular'], 'sinko-dashboard'),
			'update_item' => __('Update ' . $args_rec['singular'], 'sinko-dashboard'),
			'view_item' => __('View ' . $args_rec['singular'], 'sinko-dashboard'),
			'view_items' => __('View ' . $args_rec['plural'], 'sinko-dashboard'),
			'search_items' => __('Search ' . $args_rec['singular'], 'sinko-dashboard'),
			'not_found' => __('Not found', 'sinko-dashboard'),
			'not_found_in_trash' => __('Not found in Trash', 'sinko-dashboard'),
			'featured_image' => __('Featured Image', 'sinko-dashboard'),
			'set_featured_image' => __('Set featured image', 'sinko-dashboard'),
			'remove_featured_image' => __('Remove featured image', 'sinko-dashboard'),
			'use_featured_image' => __('Use as featured image', 'sinko-dashboard'),
			'insert_into_item' => __('Insert into ' . $args_rec['singular'], 'sinko-dashboard'),
			'uploaded_to_this_item' => __('Uploaded to this ' . $args_rec['singular'], 'sinko-dashboard'),
			'items_list' => __($args_rec['plural'] . ' list', 'sinko-dashboard'),
			'items_list_navigation' => __($args_rec['plural'] . ' list navigation', 'sinko-dashboard'),
			'filter_items_list' => __('Filter ' . $args_rec['plural'] . ' list', 'sinko-dashboard'),
		);
		$args = array(
			'label' => __($args_rec['singular'], 'sinko-dashboard'),
			'description' => __($args_rec['singular'] . ' Description', 'sinko-dashboard'),
			'labels' => $labels,
			'supports' => array('title'),
			'hierarchical' => false,
			'public' => $args_rec['is_public'],
			'show_ui' => true,
			'show_in_menu' => 'sinko-dashboard-old',
			'menu_position' => 50,
			'menu_icon' => $args_rec['icon'],
			'show_in_admin_bar' => $args_rec['is_public'],
			'show_in_nav_menus' => $args_rec['is_public'],
			'can_export' => true,
			'has_archive' => $args_rec['is_public'],
			'exclude_from_search' => $args_rec['is_public'] ? false : true,
			'publicly_queryable' => $args_rec['is_public'],
			'capability_type' => 'post',
			'show_in_rest' => true,
		);
		register_post_type(strtolower($args_rec['key']), $args);
	}

}
