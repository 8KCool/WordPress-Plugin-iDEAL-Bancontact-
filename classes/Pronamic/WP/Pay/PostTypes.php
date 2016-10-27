<?php

/**
 * Title: WordPress iDEAL post types
 * Description:
 * Copyright: Copyright (c) 2005 - 2016
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 3.7.0
 * @since 3.7.0
 */
class Pronamic_WP_Pay_PostTypes {
	/**
	 * Constructs and initializes an post types object
	 */
	public function __construct() {
		/**
		 * Priotiry of the initial post types function should be set to < 10
		 *
		 * @see https://core.trac.wordpress.org/ticket/28488
		 * @see https://core.trac.wordpress.org/changeset/29318
		 *
		 * @see https://github.com/WordPress/WordPress/blob/4.0/wp-includes/post.php#L167
		 */
		add_action( 'init', array( $this, 'register_post_types' ), 0 ); // highest priority
		add_action( 'init', array( $this, 'register_post_status' ), 9 );
	}

	//////////////////////////////////////////////////

	/**
	 * Register post types.
	 *
	 * @see https://github.com/WordPress/WordPress/blob/4.6.1/wp-includes/post.php#L1277-L1300
	 */
	public function register_post_types() {
		register_post_type( 'pronamic_gateway', array(
			'label'              => __( 'Payment Gateway Configurations', 'pronamic_ideal' ),
			'labels'             => array(
				'name'                  => __( 'Payment Gateway Configurations', 'pronamic_ideal' ),
				'singular_name'         => __( 'Payment Gateway Configuration', 'pronamic_ideal' ),
				'add_new'               => __( 'Add New', 'pronamic_ideal' ),
				'add_new_item'          => __( 'Add New Payment Gateway Configuration', 'pronamic_ideal' ),
				'edit_item'             => __( 'Edit Payment Gateway Configuration', 'pronamic_ideal' ),
				'new_item'              => __( 'New Payment Gateway Configuration', 'pronamic_ideal' ),
				'all_items'             => __( 'All Payment Gateway Configurations', 'pronamic_ideal' ),
				'view_item'             => __( 'View Payment Gateway Configuration', 'pronamic_ideal' ),
				'search_items'          => __( 'Search Payment Gateway Configurations', 'pronamic_ideal' ),
				'not_found'             => __( 'No payment gateway configurations found.', 'pronamic_ideal' ),
				'not_found_in_trash'    => __( 'No payment gateway configurations found in Trash.', 'pronamic_ideal' ),
				'menu_name'             => __( 'Configurations', 'pronamic_ideal' ),
				'filter_items_list'     => __( 'Filter payment gateway configurations list', 'pronamic_ideal' ),
				'items_list_navigation' => __( 'Payment gateway configurations list navigation', 'pronamic_ideal' ),
				'items_list'            => __( 'Payment gateway configurations list', 'pronamic_ideal' ),
			),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_nav_menus'  => false,
			'show_in_menu'       => false,
			'show_in_admin_bar'  => false,
			'hierarchical'       => true,
			'supports'           => array(
				'title',
				'revisions',
			),
			'rewrite'            => false,
			'query_var'          => false,
			'capabilities'       => Pronamic_WP_Pay_Admin_GatewayPostType::get_capabilities(),
			'map_meta_cap'       => false,
		) );

		register_post_type( 'pronamic_payment', array(
			'label'              => __( 'Payments', 'pronamic_ideal' ),
			'labels'             => array(
				'name'                  => __( 'Payments', 'pronamic_ideal' ),
				'singular_name'         => __( 'Payment', 'pronamic_ideal' ),
				'add_new'               => __( 'Add New', 'pronamic_ideal' ),
				'add_new_item'          => __( 'Add New Payment', 'pronamic_ideal' ),
				'edit_item'             => __( 'Edit Payment', 'pronamic_ideal' ),
				'new_item'              => __( 'New Payment', 'pronamic_ideal' ),
				'all_items'             => __( 'All Payments', 'pronamic_ideal' ),
				'view_item'             => __( 'View Payment', 'pronamic_ideal' ),
				'search_items'          => __( 'Search Payments', 'pronamic_ideal' ),
				'not_found'             => __( 'No payments found.', 'pronamic_ideal' ),
				'not_found_in_trash'    => __( 'No payments found in Trash.', 'pronamic_ideal' ),
				'menu_name'             => __( 'Payments', 'pronamic_ideal' ),
				'filter_items_list'     => __( 'Filter payments list', 'pronamic_ideal' ),
				'items_list_navigation' => __( 'Payments list navigation', 'pronamic_ideal' ),
				'items_list'            => __( 'Payments list', 'pronamic_ideal' ),
			),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_nav_menus'  => false,
			'show_in_menu'       => false,
			'show_in_admin_bar'  => false,
			'supports'           => false,
			'rewrite'            => false,
			'query_var'          => false,
			'capabilities'       => Pronamic_WP_Pay_Admin_PaymentPostType::get_capabilities(),
			'map_meta_cap'       => true,
		) );

		register_post_type( 'pronamic_pay_subscr', array(
			'label'              => __( 'Subscriptions', 'pronamic_ideal' ),
			'labels'             => array(
				'name'                  => __( 'Subscriptions', 'pronamic_ideal' ),
				'singular_name'         => __( 'Subscription', 'pronamic_ideal' ),
				'add_new'               => __( 'Add New', 'pronamic_ideal' ),
				'add_new_item'          => __( 'Add New Subscription', 'pronamic_ideal' ),
				'edit_item'             => __( 'Edit Subscription', 'pronamic_ideal' ),
				'new_item'              => __( 'New Subscription', 'pronamic_ideal' ),
				'all_items'             => __( 'All Subscriptions', 'pronamic_ideal' ),
				'view_item'             => __( 'View Subscription', 'pronamic_ideal' ),
				'search_items'          => __( 'Search Subscriptions', 'pronamic_ideal' ),
				'not_found'             => __( 'No subscriptions found.', 'pronamic_ideal' ),
				'not_found_in_trash'    => __( 'No subscriptions found in Trash.', 'pronamic_ideal' ),
				'menu_name'             => __( 'Subscriptions', 'pronamic_ideal' ),
				'filter_items_list'     => __( 'Filter subscriptions list', 'pronamic_ideal' ),
				'items_list_navigation' => __( 'Subscriptions list navigation', 'pronamic_ideal' ),
				'items_list'            => __( 'Subscriptions list', 'pronamic_ideal' ),
			),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_nav_menus'  => false,
			'show_in_menu'       => false,
			'show_in_admin_bar'  => false,
			'supports'           => false,
			'rewrite'            => false,
			'query_var'          => false,
			'capabilities'       => Pronamic_WP_Pay_Admin_PaymentPostType::get_capabilities(),
			'map_meta_cap'       => true,
		) );

		register_post_type( 'pronamic_pay_form', array(
			'label'              => __( 'Payment Forms', 'pronamic_ideal' ),
			'labels'             => array(
				'name'                  => __( 'Payment Forms', 'pronamic_ideal' ),
				'singular_name'         => __( 'Payment Form', 'pronamic_ideal' ),
				'add_new'               => __( 'Add New', 'pronamic_ideal' ),
				'add_new_item'          => __( 'Add New Payment Form', 'pronamic_ideal' ),
				'edit_item'             => __( 'Edit Payment Form', 'pronamic_ideal' ),
				'new_item'              => __( 'New Payment Form', 'pronamic_ideal' ),
				'all_items'             => __( 'All Payment Forms', 'pronamic_ideal' ),
				'view_item'             => __( 'View Payment Form', 'pronamic_ideal' ),
				'search_items'          => __( 'Search Payment Forms', 'pronamic_ideal' ),
				'not_found'             => __( 'No payment forms found.', 'pronamic_ideal' ),
				'not_found_in_trash'    => __( 'No payment forms found in Trash.', 'pronamic_ideal' ),
				'menu_name'             => __( 'Payment Forms', 'pronamic_ideal' ),
				'filter_items_list'     => __( 'Filter payment forms list', 'pronamic_ideal' ),
				'items_list_navigation' => __( 'Payment forms list navigation', 'pronamic_ideal' ),
				'items_list'            => __( 'Payment forms list', 'pronamic_ideal' ),
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_nav_menus'  => true,
			'show_in_menu'       => false,
			'show_in_admin_bar'  => false,
			'supports'           => array(
				'title',
				'revisions',
			),
			'rewrite'            => array(
				'slug' => _x( 'payment-forms', 'slug', 'pronamic_ideal' ),
			),
			'query_var'          => false,
			'capabilities'       => Pronamic_WP_Pay_Admin_FormPostType::get_capabilities(),
			'map_meta_cap'       => true,
		) );
	}

	/**
	 * Register our custom post statuses, used for order status.
	 */
	public function register_post_status() {
		/**
		 * Payment post statuses
		 */
		register_post_status( 'payment_pending', array(
			'label'                     => _x( 'Pending', 'Payment status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'payment_processing', array(
			'label'                     => _x( 'Processing', 'Payment status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'payment_on_hold', array(
			'label'                     => _x( 'On Hold', 'Payment status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'On Hold <span class="count">(%s)</span>', 'On Hold <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'payment_completed', array(
			'label'                     => _x( 'Completed', 'Payment status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'payment_cancelled', array(
			'label'                     => _x( 'Cancelled', 'Payment status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'payment_refunded', array(
			'label'                     => _x( 'Refunded', 'Payment status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'payment_failed', array(
			'label'                     => _x( 'Failed', 'Payment status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'payment_expired', array(
			'label'                     => _x( 'Expired', 'Payment status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		/**
		 * Subscription post statuses
		 */
		register_post_status( 'subscr_pending', array(
			'label'                     => _x( 'Pending', 'Subscription status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'subscr_cancelled', array(
			'label'                     => _x( 'Cancelled', 'Subscription status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'subscr_expired', array(
			'label'                     => _x( 'Expired', 'Subscription status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'subscr_failed', array(
			'label'                     => _x( 'Failed', 'Subscription status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'subscr_active', array(
			'label'                     => _x( 'Active', 'Subscription status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );

		register_post_status( 'subscr_completed', array(
			'label'                     => _x( 'Completed', 'Subscription status', 'pronamic_ideal' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'pronamic_ideal' ),
		) );
	}
}
