<?php

/**
 * Title: WP eCommerce IDeal Addon
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_WPeCommerce_IDeal_AddOn {
	/**
	 * Slug
	 * 
	 * @var string
	 */
	const SLUG = 'wp-e-commerce';

	//////////////////////////////////////////////////
	
	/**
	 * Bootstrap
	 */
	public static function bootstrap(){
		// Add gateway to gateways
		add_filter( 'wpsc_merchants_modules',                     array( __CLASS__, 'merchants_modules' ) );
		
		// Update payment status when returned from iDEAL
		add_action( 'pronamic_ideal_status_update',               array( __CLASS__, 'status_update' ), 10, 2 );
		
		// Source Column
		add_filter( 'pronamic_ideal_source_column_wp-e-commerce', array( __CLASS__, 'source_column' ), 10, 2 );
	}

	//////////////////////////////////////////////////
	
	/**
	 * Merchants modules
	 * 
	 * @param array $gateways
	 */
	public static function merchants_modules( $gateways ) {
		global $nzshpcrt_gateways, $num, $wpsc_gateways, $gateway_checkout_form_fields;

		$gateways[] = array(
			'name'                   => __( 'Pronamic iDEAL', 'pronamic_ideal' ),
			'api_version'            => 2.0,
			'image'                  => plugins_url( '/images/icon-32x32.png', Pronamic_WordPress_IDeal_Plugin::$file ),
			'class_name'             => 'Pronamic_WPeCommerce_IDeal_IDealMerchant',
			'has_recurring_billing'  => false,
			'wp_admin_cannot_cancel' => false,
			'display_name'           => __( 'iDEAL', 'pronamic_ideal' ),
			'requirements'           => array(
				'php_version'   => 5.0, 
				'extra_modules' => array()
			) ,
			'form'                   => 'pronamic_ideal_wpsc_merchant_form', 
			'submit_function'        => 'pronamic_ideal_wpsc_merchant_submit_function', 
			// this may be legacy, not yet decided
			'internalname'           => 'wpsc_merchant_pronamic_ideal'
		);

		$gateway_checkout_form_fields['wpsc_merchant_pronamic_ideal'] = self::advanced_inputs();

		return $gateways;
	}

	/**
	 * Advanced inputs
	 * 
	 * @return string
	 */
	private function advanced_inputs() {
		$output = '';

		$configuration_id = get_option( 'pronamic_ideal_wpsc_configuration_id' );

		$configuration = Pronamic_WordPress_IDeal_ConfigurationsRepository::getConfigurationById( $configuration_id );

		$gateway = Pronamic_WordPress_IDeal_IDeal::get_gateway( $configuration );

		if ( $gateway ) {
			$output = $gateway->get_input_html();
		}
	
		return $output;
	}
	
	//////////////////////////////////////////////////

	/**
	 * Update lead status of the specified payment
	 * 
	 * @param string $payment
	 */
	public static function status_update( $payment, $can_redirect = false ) {
		if ( $payment->getSource() == self::SLUG ) {
			$id = $payment->getSourceId();

			$merchant = new Pronamic_WPeCommerce_IDeal_IDealMerchant( $id );
			$data = new Pronamic_WPeCommerce_IDeal_IDealDataProxy( $merchant );

			$url = $data->getNormalReturnUrl();

			switch ( $payment->status ) {
				case Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_CANCELLED:
					$merchant->set_purchase_processed_by_purchid( Pronamic_WPeCommerce_WPeCommerce::PURCHASE_STATUS_INCOMPLETE_SALE );
					// $merchant->set_transaction_details( $payment->transaction->getId(), Pronamic_WPeCommerce_WPeCommerce::PURCHASE_STATUS_INCOMPLETE_SALE );

	                $url = $data->getCancelUrl();

					break;
				case Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_EXPIRED:

					break;
				case Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_FAILURE:

					break;
				case Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_SUCCESS:
	            	$merchant->set_purchase_processed_by_purchid( Pronamic_WPeCommerce_WPeCommerce::PURCHASE_STATUS_ACCEPTED_PAYMENT );

	                $url = $data->getSuccessUrl();

					break;
				case Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_OPEN:

					break;
				default:

					break;
			}
			
			if ( $can_redirect ) {
				wp_redirect( $url, 303 );

				exit;
			}
		}
	}

	//////////////////////////////////////////////////
	
	/**
	 * Source column
	 */
	public static function source_column( $text, $payment ) {
		$text  = '';

		$text .= __( 'WP e-Commerce', 'pronamic_ideal' ) . '<br />';

		$text .= sprintf(
			'<a href="%s">%s</a>',
			add_query_arg( array(
				'page'           => 'wpsc-sales-logs', 
				'purchaselog_id' => $payment->getSourceId()
			), admin_url( 'index.php' ) ),
			sprintf( __( 'Purchase #%s', 'pronamic_ideal' ), $payment->getSourceId() )
		);

		return $text;
	}
}
