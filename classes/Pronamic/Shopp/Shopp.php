<?php 

/**
 * Title: Shopp
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_Shopp_Shopp {
	/**
	 * Payment status pending
	 * 
	 * @var string
	 */
	const PAYMENT_STATUS_PENDING = 'PENDING';

	/**
	 * Payment status expired
	 * 
	 * @var string
	 */
	const PAYMENT_STATUS_EXPIRED = 'EXPIRED';

	/**
	 * Payment status cancelled
	 * 
	 * @var string
	 */
	const PAYMENT_STATUS_CANCELLED = 'CANCELLED';

	/**
	 * Payment status failure
	 * 
	 * @var string
	 */
	const PAYMENT_STATUS_FAILURE = 'FAILURE';

	/**
	 * Payment status charged
	 * 
	 * @var string
	 */
	const PAYMENT_STATUS_CHARGED = 'CHARGED';

	/**
	 * Payment status charged
	 * 
	 * @var string
	 */
	const PAYMENT_STATUS_OPEN = 'OPEN';
	
	//////////////////////////////////////////////////

	/**
	 * Payment status authed
	 * 
	 * @since Shopp v1.2
	 * @var string
	 */
	const PAYMENT_STATUS_AUTHED = 'authed';

	/**
	 * Payment status captured
	 *
	 * @since Shopp v1.2
	 * @var string
	 */
	const PAYMENT_STATUS_CAPTURED = 'captured';

	//////////////////////////////////////////////////

	/**
	 * Check if Shopp is active (Automattic/developer style)
	 *
	 * @see https://bitbucket.org/Pronamic/shopp/src/12ebdb1d82a029bed956a58135833e3507baf432/Shopp.php?at=1.2.9#cl-29
	 * @see https://github.com/Automattic/developer/blob/1.1.2/developer.php#L73
	 *
	 * @return boolean
	 */
	public static function is_active() {
		return defined( 'SHOPP_VERSION' );
	}
	
	//////////////////////////////////////////////////

	/**
	 * Check if the purchase is paid
	 * 
	 * @param Purchase $purchase
	 */
	public static function is_purchase_paid( $purchase ) {
		$is_paid = false;

		if ( version_compare( SHOPP_VERSION, '1.2', '<' ) ) {
			// In Shopp < 1.2 an paid purchase has not the status 'PENDING'
			$is_paid = ! in_array(
				$purchase->txnstatus,
				array(
					self::PAYMENT_STATUS_PENDING
				)
			);
		} else {
			// In Shopp >= 1.2 an paid purchase has the 'captured' status
			$is_paid = in_array(
				$purchase->txnstatus,
				array(
					self::PAYMENT_STATUS_CAPTURED
				)
			);
		}
		
		return $is_paid;
	}
	
	//////////////////////////////////////////////////

	/**
	 * Update purchase status
	 * 
	 * @param Purchase $purchase
	 * @param string $status
	 */
	public static function update_purchase_status( $purchase, $status ) {
		global $wpdb;

		$wpdb->update(
			$wpdb->prefix . SHOPP_DBPREFIX . 'purchase',
			array( 'txnstatus' => $status ),
			array( 'id' => $purchase->id )
		);
	}
}
