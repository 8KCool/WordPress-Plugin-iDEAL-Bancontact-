<?php

function pronamic_wp_pay_update_subscription( Pronamic_WP_Pay_Subscription $subscription ) {
	$post_id = $subscription->get_id();

	// Meta
	$prefix = '_pronamic_subscription_';

	$meta = array_merge(
		array(
			'transaction_id'          => $subscription->get_transaction_id(),
			'status'                  => $subscription->get_status(),
		),
		$subscription->meta
	);

	foreach ( $meta as $key => $value ) {
		if ( ! empty( $value ) ) {
			$meta_key = $prefix . $key;

			update_post_meta( $post_id, $meta_key, $value );
		}
	}

	$status = get_post_meta( $post_id, '_pronamic_subscription_status', true );

	$post_status = null;

	switch ( $status ) {
		case Pronamic_WP_Pay_Statuses::OPEN :
			$post_status = 'sub_pending';

			break;
		case Pronamic_WP_Pay_Statuses::CANCELLED :
			$post_status = 'sub_cancelled';

			break;
		case Pronamic_WP_Pay_Statuses::EXPIRED :
			$post_status = 'sub_expired';

			break;
		case Pronamic_WP_Pay_Statuses::FAILURE :
			$post_status = 'sub_failed';

			break;
		case Pronamic_WP_Pay_Statuses::SUCCESS :
			$post_status = 'sub_active';

			break;
		case Pronamic_WP_Pay_Statuses::COMPLETED :
			$post_status = 'sub_completed';

			break;
	}

	if ( null !== $post_status ) {
		wp_update_post( array(
			'ID'          => $subscription->post->ID,
			'post_status' => $post_status,
		) );
	}
}
