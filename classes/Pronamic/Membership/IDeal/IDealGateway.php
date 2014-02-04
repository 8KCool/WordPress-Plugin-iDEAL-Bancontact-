<?php

/**
 * Title: Membership iDEAL gateway
 * Copyright: Pronamic (c) 2005 - 2013
 * Company: Pronamic
 * @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/gateways/gateway.freesubscriptions.php
 * @author Leon Rowland <leon@rowland.nl>
 * @version 1.0
 */
class Pronamic_Membership_IDeal_IDealGateway extends M_Gateway {
	/**
	 * Gateway name/slug
	 * 
	 * @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/classes/class.gateway.php#L10
	 * @var string
	 */
	public $gateway = 'ideal';

	/**
	 * Gateway title
	 * 
	 * @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/classes/class.gateway.php#L11
	 * @var string
	 */
	public $title = 'iDEAL';
	
	//////////////////////////////////////////////////

	/**
	 * Constructs and initliaze an Membership iDEAL gateway
	 * 
	 * Warning: The constructor of this class can not be named '__construct'. 
	 * The M_Gateway class is calling the '__construct' method wich will cause
	 * an infinite loop and an 'Fatal error: Allowed memory size'.
	 */
	public function Pronamic_Membership_IDeal_IDealGateway() {
		parent::M_Gateway();

		// @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/gateways/gateway.freesubscriptions.php#L30
		// @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/classes/class.gateway.php#L97
		if ( $this->is_active() ) {
			add_action( 'init', array( $this, 'maybe_pay' ) );
			
			// @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/includes/payment.form.php#L78
			add_action( 'membership_purchase_button', array( $this, 'purchase_button' ), 1, 3 );

			// Status update
			$slug = Pronamic_Membership_IDeal_AddOn::SLUG;

			add_action( "pronamic_payment_status_update_$slug", array( $this, 'status_update' ), 10, 2 );
		}
	}
	
	//////////////////////////////////////////////////

	/**
	 * Purchase button
	 * 
	 * @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/includes/payment.form.php#L78
	 * 
	 * @param M_Subscription $subscription
	 *     @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/classes/class.subscription.php
	 *     
	 * @param array $pricing
	 *     @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/classes/class.subscription.php#L110
	 *     
	 *     array(
	 *         array(
	 *             'period' => '1',
	 *             'amount' => '50.00',
	 *             'type'   => 'indefinite',
	 *             'unit'   => 'm'
	 *         )
	 *     )
	 *     
	 * @param int $user_id WordPress user/member ID
	 */
	public function purchase_button( $subscription, $pricing, $user_id ) {
		if ( Pronamic_Membership_Membership::is_pricing_free( $pricing ) ) {
			// @todo what todo?
		} else {
			$membership = new M_Membership( $user_id );

			$config_id = get_option( Pronamic_Membership_IDeal_AddOn::OPTION_CONFIG_ID );

			$data = new Pronamic_WP_Pay_Membership_PaymentData( $subscription, $membership );
	
			$gateway = Pronamic_WordPress_IDeal_IDeal::get_gateway( $config_id );

			if ( $gateway ) {
				if ( filter_has_var( INPUT_POST, 'pronamic_pay_membership' ) ) {
					// Start
					$payment = Pronamic_WordPress_IDeal_IDeal::start( $config_id, $gateway, $data );

					update_post_meta( $payment->get_id(), '_pronamic_payment_membership_user_id', $user_id );
					update_post_meta( $payment->get_id(), '_pronamic_payment_membership_subscription_id', $data->get_subscription_id() );
					
					// Membership record transaction
					// @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/classes/class.gateway.php#L176
					$this->record_transaction(
						$user_id, // User ID
						$data->get_subscription_id(), // Sub ID
						$data->get_amount(), // Amount
						$data->get_currency(), // Currency
						time(), // Timestamp
						$payment->get_id(), // PayPal ID
						'', // Status
						'' // Note
					);

					// Redirect
					$gateway->redirect( $payment );
				} else {
                    global $M_options;

                    // @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/classes/membershipadmin.php#K2908
                    if ( isset( $M_options['formtype'] ) && 'new' == strtolower( $M_options['formtype'] ) ) {
                        $action = add_query_arg( array(
                        	'action'       => 'buynow',
                        	'subscription' => $data->get_subscription_id()
                        ), admin_url( 'admin-ajax.php' ) );
                    } else {
                        $action = '#pronamic-pay-form';
                    }

                    printf(
                        '<form id="pronamic-pay-form" method="post" action="%s">',
                        $action
                    );
						
					printf(
						'<img src="%s" alt="%s" />',
						esc_attr( plugins_url( 'images/ideal-logo-pay-off-2-lines.png', Pronamic_WordPress_IDeal_Plugin::$file ) ),
						esc_attr__( 'iDEAL - Online payment through your own bank', 'pronamic_ideal' )
					);
		
					echo '<div style="margin-top: 1em;">';
	
					echo $gateway->get_input_html();

					// Coupon
					$coupon = membership_get_current_coupon();
					
					if ( $coupon ) {
						printf(
							'<input type="hidden" name="coupon_code" id="subscription_coupon_code" value="%s" />',
							esc_attr( $coupon->get_coupon_code() )
						);
					}

					// Submit button
					printf(
						'<input type="submit" name="pronamic_pay_membership" value="%s" />',
						esc_attr__( 'Pay', 'pronamic_ideal' )
					);
	
					echo '</div>';
		
					printf( '</form>' );
				}
			}
		}
	}
	
	//////////////////////////////////////////////////

	/**
	 * Maybe pay
	 */
	public function maybe_pay() {
		if ( filter_has_var( INPUT_POST, 'pronamic_pay_membership' ) ) {
			// Start output buffering so the Membership purchase button can
			// handle the payment
			ob_start();
		}
	}
	
	//////////////////////////////////////////////////

	/**
	 * Status update
	 */
	public function status_update( Pronamic_Pay_Payment $payment, $can_redirect = false ) {
		$user_id  = get_post_meta( $payment->get_id(), '_pronamic_payment_membership_user_id', true );
		$sub_id   = get_post_meta( $payment->get_id(), '_pronamic_payment_membership_subscription_id', true );
		$amount   = $payment->get_amount();
		$currency = $payment->get_currency();
		$status   = $payment->get_status();
		$note     = '';

		// Membership record transaction
		// @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/classes/class.gateway.php#L176
		$this->record_transaction( $user_id, $sub_id, $amount, $currency, time(), $payment->get_id(), $status, $note );
		
		switch ( $status ) {
			case Pronamic_Pay_Gateways_IDeal_Statuses::CANCELLED:
				
				break;
			case Pronamic_Pay_Gateways_IDeal_Statuses::EXPIRED:
				
				break;
			case Pronamic_Pay_Gateways_IDeal_Statuses::FAILURE:
				
				break;
			case Pronamic_Pay_Gateways_IDeal_Statuses::OPEN:
				// @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/gateways/gateway.paypalexpress.php#L871
				do_action( 'membership_payment_pending', $user_id, $sub_id, $amount, $currency, $payment->get_id() );
				
				break;
			case Pronamic_Pay_Gateways_IDeal_Statuses::SUCCESS:
				$member = new M_Membership( $user_id );
				if ( $member ) {
					$member->create_subscription( $sub_id, $this->gateway );
				}

				// Added for affiliate system link
				// @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/gateways/gateway.paypalexpress.php#L790
				do_action( 'membership_payment_processed', $user_id, $sub_id, $amount, $currency, $payment->get_id() );
				
				// @see http://plugins.trac.wordpress.org/browser/membership/tags/3.4.4.1/membershipincludes/gateways/gateway.paypalexpress.php#L901
				do_action( 'membership_payment_subscr_signup', $user_id, $sub_id );

				break;
		}
	}
}
