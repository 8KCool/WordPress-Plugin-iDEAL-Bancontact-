<?php

/**
 * Title: WordPress admin payment post type
 * Description:
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_WP_Pay_Admin_PaymentPostType {
	public function __construct() {
		$post_type = 'pronamic_payment';

		add_filter( 'manage_edit-' . $post_type . '_columns', array( $this, 'edit_columns' ) );

		add_action( 'manage_' . $post_type . '_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );

		add_action( 'save_post_' . $post_type, array( $this, 'save_post' ) );
	}

	//////////////////////////////////////////////////

	public function edit_columns( $columns ) {
		$columns = array(
			'cb'                           => '<input type="checkbox" />',
			'title'                        => __( 'Title', 'pronamic_ideal' ),
			'pronamic_payment_gateway'     => __( 'Gateway', 'pronamic_ideal' ),
			'pronamic_payment_transaction' => __( 'Transaction', 'pronamic_ideal' ),
			'pronamic_payment_description' => __( 'Description', 'pronamic_ideal' ),
			'pronamic_payment_amount'      => __( 'Amount', 'pronamic_ideal' ),
			'pronamic_payment_consumer'    => __( 'Consumer', 'pronamic_ideal' ),
			'pronamic_payment_source'      => __( 'Source', 'pronamic_ideal' ),
			'pronamic_payment_status'      => __( 'Status', 'pronamic_ideal' ),
			'author'                       => __( 'Author', 'pronamic_ideal' ),
			'date'                         => __( 'Date', 'pronamic_ideal' ),
		);

		return $columns;
	}

	public function custom_columns( $column, $post_id ) {
		global $post;

		switch ( $column ) {
			case 'pronamic_payment_gateway':
				$config_id = get_post_meta( $post_id, '_pronamic_payment_config_id', true );

				if ( ! empty( $config_id ) ) {
					echo get_the_title( $config_id );
				} else {
					echo '—';
				}

				break;
			case 'pronamic_payment_transaction':
				echo get_post_meta( $post_id, '_pronamic_payment_transaction_id', true );

				break;
			case 'pronamic_payment_description':
				echo get_post_meta( $post_id, '_pronamic_payment_description', true );

				break;
			case 'pronamic_payment_amount':
				echo get_post_meta( $post_id, '_pronamic_payment_currency', true );
				echo ' ';
				echo get_post_meta( $post_id, '_pronamic_payment_amount', true );

				break;
			case 'pronamic_payment_consumer':
				echo get_post_meta( $post_id, '_pronamic_payment_consumer_name', true );
				echo '<br />';
				echo get_post_meta( $post_id, '_pronamic_payment_consumer_account_number', true );
				echo get_post_meta( $post_id, '_pronamic_payment_consumer_iban', true );
				echo get_post_meta( $post_id, '_pronamic_payment_consumer_bic', true );
				echo '<br />';
				echo get_post_meta( $post_id, '_pronamic_payment_consumer_city', true );

				break;
			case 'pronamic_payment_source':
				$payment = get_pronamic_payment( $post_id );

				echo $payment->get_source_text();

				break;
			case 'pronamic_payment_status':
				$status = get_post_meta( $post_id, '_pronamic_payment_status', true );

				echo Pronamic_WP_Pay_Plugin::translate_status( $status );

				break;
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Add meta boxes
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'pronamic_payment',
			__( 'Payment', 'pronamic_ideal' ),
			array( $this, 'meta_box_info' ),
			'pronamic_payment',
			'normal',
			'high'
		);

		add_meta_box(
			'pronamic_payment_source',
			__( 'Source', 'pronamic_ideal' ),
			array( $this, 'meta_box_source' ),
			'pronamic_payment',
			'normal',
			'high'
		);

		add_meta_box(
			'pronamic_payment_log',
			__( 'Log', 'pronamic_ideal' ),
			array( $this, 'meta_box_log' ),
			'pronamic_payment',
			'normal',
			'high'
		);

		add_meta_box(
			'pronamic_payment_update',
			__( 'Update', 'pronamic_ideal' ),
			array( $this, 'meta_box_update' ),
			'pronamic_payment',
			'side',
			'high'
		);

		// @see http://kovshenin.com/2012/how-to-remove-the-publish-box-from-a-post-type/
		remove_meta_box( 'submitdiv', 'pronamic_payment', 'side' );
	}

	/**
	 * Pronamic Pay gateway config meta box
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function meta_box_info( $post ) {
		include Pronamic_WP_Pay_Plugin::$dirname . '/admin/meta-box-payment-info.php';
	}

	/**
	 * Pronamic Pay gateway config meta box
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function meta_box_source( $post ) {
		include Pronamic_WP_Pay_Plugin::$dirname . '/admin/meta-box-payment-source.php';
	}

	/**
	 * Pronamic Pay gateway config meta box
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function meta_box_log( $post ) {
		include Pronamic_WP_Pay_Plugin::$dirname . '/admin/meta-box-payment-log.php';
	}

	/**
	 * Pronamic Pay gateway update meta box
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function meta_box_update( $post ) {
		include Pronamic_WP_Pay_Plugin::$dirname . '/admin/meta-box-payment-update.php';
	}

	//////////////////////////////////////////////////

	/**
	 * Post row actions
	 */
	public function post_row_actions( $actions, $post ) {
		if ( 'pronamic_payment' === $post->post_type ) {
			return array();
		}

		return $actions;
	}

	//////////////////////////////////////////////////

	/**
	 * Save post
	 *
	 * @see https://github.com/WordPress/WordPress/blob/4.2.3/wp-includes/post.php#L3518-L3530
	 */
	public function save_post( $post_id ) {
		if ( filter_has_var( INPUT_POST, 'pronamic_payment_update' ) ) {
			$nonce = filter_input( INPUT_POST, 'pronamic_payment_nonce', FILTER_SANITIZE_STRING );

			if ( wp_verify_nonce( $nonce, 'pronamic_payment_update' ) ) {
				$payment = get_pronamic_payment( $post_id );

				$can_redirect = false;

				$status_old = $payment->status;
				$status_new = filter_input( INPUT_POST, 'pronamic_payment_status', FILTER_SANITIZE_STRING );

				$payment->status = $status_new;

				pronamic_wp_pay_update_payment( $payment );

				do_action( 'pronamic_payment_status_update_' . $payment->source . '_' . $status_old . '_to_' . $status_new, $payment, $can_redirect );
				do_action( 'pronamic_payment_status_update_' . $payment->source, $payment, $can_redirect );
				do_action( 'pronamic_payment_status_update', $payment, $can_redirect );
			}
		}
	}
}
