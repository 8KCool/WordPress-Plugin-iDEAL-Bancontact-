<?php

/**
 * Title: Gravity Forms iDEAL Add-On
 * Description:
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_GravityForms_IDeal_AddOn {
	/**
	 * Slug
	 *
	 * @var string
	 */
	const SLUG = 'gravityformsideal';

	/**
	 * Gravity Forms minimum required version
	 *
	 * @var string
	 */
	const GRAVITY_FORMS_MINIMUM_VERSION = '1.0';

	//////////////////////////////////////////////////

	/**
	 * Bootstrap
	 */
	public static function bootstrap() {
		// Initialize hook, Gravity Forms uses the default priority (10)
		add_action( 'init', array( __CLASS__, 'init' ), 20 );
		
		add_action( 'pronamic_pay_upgrade', array( __CLASS__, 'upgrade' ) );
	}

	//////////////////////////////////////////////////

	/**
	 * Initialize
	 */
	public static function init() {
		if ( self::is_gravityforms_supported() ) {
			// Admin
			if ( is_admin() ) {
				Pronamic_GravityForms_IDeal_Admin::bootstrap();
			} else {
				// @see http://www.gravityhelp.com/documentation/page/Gform_confirmation
				add_filter( 'gform_confirmation',     array( __CLASS__, 'handle_ideal' ), 10, 4 );

	            // Set entry meta after submission
	            add_action( 'gform_after_submission', array( __CLASS__, 'set_entry_meta' ), 5, 2 );

	            add_action( 'gform_after_submission', array( __CLASS__, 'maybe_delay_campaignmonitor_subscription' ), 1, 2 );
	            add_action( 'gform_after_submission', array( __CLASS__, 'maybe_delay_mailchimp_subscription' ), 1, 2 );

	            // Delay
	            add_filter( 'gform_disable_admin_notification', array( __CLASS__, 'maybe_delay_admin_notification' ), 10, 3 );
	            add_filter( 'gform_disable_user_notification',  array( __CLASS__, 'maybe_delay_user_notification' ), 10, 3 );
				add_filter( 'gform_disable_post_creation',      array( __CLASS__, 'maybe_delay_post_creation' ), 10, 3 );
				add_filter( 'gform_disable_notification',		array( __CLASS__, 'maybe_delay_notification' ), 10, 4 );
			}

			$slug = self::SLUG;

			add_action( "pronamic_payment_status_update_$slug", array( __CLASS__, 'update_status' ), 10, 2 );
			add_filter( "pronamic_payment_source_text_$slug",   array( __CLASS__, 'source_text' ), 10, 2 );

			add_filter( 'gform_replace_merge_tags', array( __CLASS__, 'replace_merge_tags' ), 10, 7 );

			// iDEAL fields
			Pronamic_GravityForms_IDeal_Fields::bootstrap();
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Source column
	 */
	public static function source_text( $text, Pronamic_Pay_Payment $payment ) {
		$text  = '';

		$text .= __( 'Gravity Forms', 'pronamic_ideal' ) . '<br />';

		$text .= sprintf(
			'<a href="%s">%s</a>',
			add_query_arg( array( 'pronamic_gf_lid' => $payment->get_source_id() ), admin_url( 'admin.php' ) ),
			sprintf( __( 'Entry #%s', 'pronamic_ideal' ), $payment->get_source_id() )
		);

		return $text;
	}

	//////////////////////////////////////////////////

	/**
	 * Upgrade
	 */
	public static function upgrade() {
		if ( self::is_gravityforms_supported() ) {
			// Add some new capabilities
			$capabilities = array(
				'read'               => true,
				'gravityforms_ideal' => true
			);

			$roles = array(
				'pronamic_ideal_administrator' => array(
					'display_name' => __( 'iDEAL Administrator', 'pronamic_ideal' ),
					'capabilities' => $capabilities
				) ,
				'administrator' => array(
					'display_name' => __( 'Administrator', 'pronamic_ideal' ),
					'capabilities' => $capabilities
				)
			);

			Pronamic_WordPress_IDeal_Plugin::set_roles( $roles );
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Maybe update user role of the specified lead and feed
	 *
	 * @param array $lead
	 * @param Feed $feed
	 */
	private static function maybe_update_user_role( $lead, $feed ) {
		$user = false;

		// Gravity Forms User Registration Add-On
		if ( class_exists( 'GFUserData' ) ) {
			$user = GFUserData::get_user_by_entry_id( $lead['id'] );
		}

		if ( $user == false ) {
			$created_by = $lead[Pronamic_GravityForms_GravityForms::LEAD_PROPERY_CREATED_BY];

			$user = new WP_User( $created_by );
		}

		if ( $user && ! empty( $feed->user_role_field_id ) && isset( $lead[$feed->user_role_field_id] ) ) {
			$value = $lead[$feed->user_role_field_id];
			$value = GFCommon::get_selection_value( $value );

			$user->set_role( $value );
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Update lead status of the specified payment
	 *
	 * @param string $payment
	 */
	public static function update_status( Pronamic_Pay_Payment $payment, $can_redirect = false ) {
		$lead_id = $payment->get_source_id();

		$lead = RGFormsModel::get_lead( $lead_id );

		if ( $lead ) {
			$form_id = $lead['form_id'];

			$form = RGFormsModel::get_form( $form_id );
			$feed = get_pronamic_gf_pay_feed_by_form_id( $form_id );
			
			$data = new Pronamic_WP_Pay_GravityForms_PaymentData( $form, $lead, $feed );

			if ( $feed ) {
				$url = null;

				switch ( $payment->status ) {
					case Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_CANCELLED:
						$lead[Pronamic_GravityForms_GravityForms::LEAD_PROPERTY_PAYMENT_STATUS] = Pronamic_GravityForms_GravityForms::PAYMENT_STATUS_CANCELLED;

						$url = $data->get_cancel_url();

						break;
					case Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_EXPIRED:
						$lead[Pronamic_GravityForms_GravityForms::LEAD_PROPERTY_PAYMENT_STATUS] = Pronamic_GravityForms_GravityForms::PAYMENT_STATUS_EXPIRED;

						$url = $feed->get_url( Pronamic_GravityForms_IDeal_Feed::LINK_EXPIRED );

						break;
					case Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_FAILURE:
						$lead[Pronamic_GravityForms_GravityForms::LEAD_PROPERTY_PAYMENT_STATUS] = Pronamic_GravityForms_GravityForms::PAYMENT_STATUS_FAILED;

						$url = $data->get_error_url();

						break;
					case Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_SUCCESS:
						if ( ! Pronamic_GravityForms_IDeal_Entry::is_payment_approved( $lead ) ) {
							// Only fullfill order if the payment isn't approved aloready
							$lead[Pronamic_GravityForms_GravityForms::LEAD_PROPERTY_PAYMENT_STATUS] = Pronamic_GravityForms_GravityForms::PAYMENT_STATUS_APPROVED;

							self::fulfill_order( $lead );
						}

						$url = $data->get_success_url();

						break;
					case Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_OPEN:
					default:
						$url = $data->get_normal_return_url();

						break;
				}

				RGFormsModel::update_lead( $lead );

				if ( $url && $can_redirect ) {
					wp_redirect( $url, 303 );

					exit;
				}
			}
		}
	}

	/**
	 * Fulfill order
	 *
	 * @param array $entry
	 */
    public static function fulfill_order( $entry ) {
		$feed = get_pronamic_gf_pay_feed_by_form_id( $entry['form_id'] );

		if ( null !== $feed ) {
			self::maybe_update_user_role( $entry, $feed );

			$form = RGFormsModel::get_form_meta( $entry['form_id'] );

			// Determine if the feed has Gravity Form 1.7 Feed IDs
			if ( $feed->has_delayed_notifications() ) {
				// @see https://bitbucket.org/Pronamic/gravityforms/src/42773f75ad7ad9ac9c31ce149510ff825e4aa01f/common.php?at=1.7.8#cl-1512
				GFCommon::send_notifications( $feed->delay_notification_ids, $form, $entry, true, 'form_submission' );
			}

			if ( $feed->delay_admin_notification ) {
				// https://bitbucket.org/Pronamic/gravityforms/src/42773f75ad7ad9ac9c31ce149510ff825e4aa01f/common.php?at=1.7.8#cl-1336
				GFCommon::send_admin_notification( $form_meta, $entry );
			}

			if ( $feed->delay_user_notification ) {
				// https://bitbucket.org/Pronamic/gravityforms/src/42773f75ad7ad9ac9c31ce149510ff825e4aa01f/common.php?at=1.7.8#cl-1329
				GFCommon::send_user_notification( $form_meta, $entry );
			}

			if ( $feed->delay_post_creation ) {
				RGFormsModel::create_post( $form_meta, $entry );
			}

			if ( $feed->delay_campaignmonitor_subscription && method_exists( 'GFCampaignMonitor', 'export' ) ) {
				call_user_func( array( 'GFCampaignMonitor', 'export' ), $entry, $form, true );
			}

			if ( $feed->delay_mailchimp_subscription && method_exists( 'GFMailChimp', 'export' ) ) {
				call_user_func( array( 'GFMailChimp', 'export' ), $entry, $form, true );
			}
		}

		// The Gravity Forms PayPal Add-On executes the 'gform_paypal_fulfillment' action
		do_action( 'gform_ideal_fulfillment', $entry, $feed );
    }

	//////////////////////////////////////////////////

	/**
	 * Checks if Gravity Forms is supported
	 *
	 * @return true if Gravity Forms is supported, false otherwise
	 */
	public static function is_gravityforms_supported() {
		if ( class_exists( 'GFCommon' ) ) {
			return version_compare( GFCommon::$version, self::GRAVITY_FORMS_MINIMUM_VERSION, '>=' );
        } else {
			return false;
        }
	}

	//////////////////////////////////////////////////

	/**
	 * Check if the iDEAL condition is true
	 *
	 * @param mixed $form
	 * @param mixed $feed
	 */
	public static function is_condition_true( $form, $feed ) {
		$result = true;

        if ( $feed->condition_enabled ) {
			$field = RGFormsModel::get_field( $form, $feed->condition_field_id );

			if ( empty( $field ) ) {
				// unknown field
				$result = true;
			} else {
				$is_hidden = RGFormsModel::is_field_hidden( $form, $field, array() );

				if ( $is_hidden ) {
					// if conditional is enabled, but the field is hidden, ignore conditional
					$result = false;
				} else {
					$value = RGFormsModel::get_field_value( $field, array() );

					$is_match = RGFormsModel::is_value_match( $value, $feed->condition_value );

					switch ( $feed->condition_operator ) {
						case Pronamic_GravityForms_GravityForms::OPERATOR_IS:
							$result = $is_match;
							break;
						case Pronamic_GravityForms_GravityForms::OPERATOR_IS_NOT:
							$result = ! $is_match;
							break;
						default: // unknown operator
							$result = true;
							break;
					}
				}
			}
        } else {
        	// condition is disabled, result is true
        	$result = true;
        }

        return $result;
	}

	//////////////////////////////////////////////////
	// Maybe delay functions
	//////////////////////////////////////////////////

	public static function maybe_delay_notification( $is_disabled, $notification, $form, $entry ) {
		$is_disabled = false;

		$feed = get_pronamic_gf_pay_feed_by_form_id( $form['id'] );

		if ( null !== $feed ) {
			if ( self::is_condition_true( $form, $feed ) ) {
				$notification_ids = $feed->delay_notification_ids;

				$is_disabled = in_array( $notification['id'], $notification_ids );
			}
		}

		return $is_disabled;
	}

	/**
	 * Maybe delay admin notification
	 *
	 * @param boolean $isDisabled
	 * @param array $form
	 * @param array $lead
	 * @return boolean true if admin notification is disabled / delayed, false otherwise
	 */
	public static function maybe_delay_admin_notification( $is_disabled, $form, $lead ) {
		$is_disabled = false;

		$feed = get_pronamic_gf_pay_feed_by_form_id( $form['id'] );

		if ( null !== $feed ) {
			if ( self::is_condition_true( $form, $feed ) ) {
				$is_disabled = $feed->delay_admin_notification;
			}
		}

		return $is_disabled;
	}

	/**
	 * Maybe delay user notification
	 *
	 * @param boolean $isDisabled
	 * @param array $form
	 * @param array $lead
	 * @return boolean true if user notification is disabled / delayed, false otherwise
	 */
	public static function maybe_delay_user_notification( $is_disabled, $form, $lead ) {
		$is_disabled = false;

		$feed = get_pronamic_gf_pay_feed_by_form_id( $form['id'] );

		if ( null !== $feed ) {
			if ( self::is_condition_true( $form, $feed ) ) {
				$is_disabled = $feed->delay_user_notification;
			}
		}

		return $is_disabled;
	}

	/**
	 * Maybe delay post creation
	 *
	 * @param boolean $is_disabled
	 * @param array $form
	 * @param array $lead
	 * @return boolean true if post creation is disabled / delayed, false otherwise
	 */
	public static function maybe_delay_post_creation( $is_disabled, $form, $lead ) {
		$is_disabled = false;

		$feed = get_pronamic_gf_pay_feed_by_form_id( $form['id'] );

		if ( null !== $feed ) {
			if ( self::is_condition_true( $form, $feed ) ) {
				$is_disabled = $feed->delay_post_creation;
			}
		}

		return $is_disabled;
	}

	//////////////////////////////////////////////////

	/**
	 * Maybe delay Campaign Monitor subscription
	 */
	public static function maybe_delay_campaignmonitor_subscription( $entry, $form ) {
		$feed = get_pronamic_gf_pay_feed_by_form_id( $form['id'] );

		if ( null !== $feed ) {
			if ( $feed->delay_campaignmonitor_subscription ) {
				remove_action( 'gform_after_submission', array( 'GFCampaignMonitor', 'export' ), 10, 2);
			}
		}
	}

	/**
	 * Maybe delay MailChimp subscription
	 */
	public static function maybe_delay_mailchimp_subscription( $entry, $form ) {
		$feed = get_pronamic_gf_pay_feed_by_form_id( $form['id'] );

		if ( null !== $feed ) {
			if ( $feed->delay_mailchimp_subscription ) {
				remove_action( 'gform_after_submission', array( 'GFMailChimp', 'export' ), 10, 2);
			}
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Set entry meta
	 *
	 * @param array $entry
	 * @param array $form
	 */
    public static function set_entry_meta( $entry, $form ) {
		// ignore requests that are not the current form's submissions
		if ( rgpost( 'gform_submit' ) != $form['id'] ) {
			return;
		}

		$feed = get_pronamic_gf_pay_feed_by_form_id( $form['id'] );
		if ( null !== $feed ) {
			// Update form meta with current feed id
			gform_update_meta( $entry['id'], 'ideal_feed_id', $feed->id );

			// Update form meta with current payment gateway
			gform_update_meta( $entry['id'], 'payment_gateway', 'ideal' );
		}
    }

	//////////////////////////////////////////////////

	/**
	 * Handle iDEAL
	 *
	 * @see http://www.gravityhelp.com/documentation/page/Gform_confirmation
	 */
	public static function handle_ideal( $confirmation, $form, $lead, $ajax ) {
		$feed = get_pronamic_gf_pay_feed_by_form_id( $form['id'] );

		if ( null !== $feed ) {
			if ( self::is_condition_true( $form, $feed ) ) {
				$gateway = Pronamic_WordPress_IDeal_IDeal::get_gateway( $feed->config_id );

				if ( $gateway ) {
					$data = new Pronamic_WP_Pay_GravityForms_PaymentData( $form, $lead, $feed );
					
					$payment = Pronamic_WordPress_IDeal_IDeal::start( $feed->config_id, $gateway, $data );

					$error = $gateway->get_error();
					
					if ( is_wp_error( $error ) ) {
						$html = '';
					
						$html .= '<ul>';
						$html .= '<li>' . Pronamic_WordPress_IDeal_IDeal::get_default_error_message() . '</li>';

						foreach ( $error->get_error_messages() As $message ) {
							$html .= '<li>' . $message . '</li>';
						}
					
						$html .= '</ul>';
					
						$confirmation = $html;
					} else {
						// Updating lead's payment_status to Processing
						$lead[Pronamic_GravityForms_GravityForms::LEAD_PROPERTY_PAYMENT_STATUS]   = Pronamic_GravityForms_GravityForms::PAYMENT_STATUS_PROCESSING;
						$lead[Pronamic_GravityForms_GravityForms::LEAD_PROPERTY_PAYMENT_AMOUNT]   = $data->get_amount();
						$lead[Pronamic_GravityForms_GravityForms::LEAD_PROPERTY_PAYMENT_DATE]     = gmdate( 'y-m-d H:i:s' );
						$lead[Pronamic_GravityForms_GravityForms::LEAD_PROPERTY_TRANSACTION_TYPE] = Pronamic_GravityForms_GravityForms::TRANSACTION_TYPE_PAYMENT;
						$lead[Pronamic_GravityForms_GravityForms::LEAD_PROPERTY_TRANSACTION_ID]   = $payment->get_transaction_id();
						
						gform_update_meta( $lead['id'], 'pronamic_payment_id', $payment->get_id() );

						RGFormsModel::update_lead( $lead );

						if ( $gateway->is_http_redirect() ) {
							// Redirect user to the issuer
							$confirmation = array( 'redirect' => $payment->get_action_url() );
						}

						if ( $gateway->is_html_form() ) {
					        // HTML
					        $html  = '';
					        $html .= '<div id="gforms_confirmation_message">';
					        $html .= 	GFCommon::replace_variables( $form['confirmation']['message'], $form, $lead, false, true, true );
					        $html .= 	$gateway->get_form_html( $payment, true );
							$html .= '</div>';
					
					        // Extend the confirmation with the iDEAL form
					        $confirmation = $html;
						}
					}
				}
			}
		}

		if ( ( headers_sent() || $ajax ) && is_array( $confirmation ) && isset( $confirmation['redirect'] ) ) {
			$url = $confirmation['redirect'];

			// Using esc_js() and esc_url() on the URL is causing problems, the & in the URL is modified to &amp; or &#038;
			$confirmation = sprintf( '<script>function gformRedirect(){document.location.href = %s;}', json_encode( $url ) );
			if ( !$ajax ) {
				$confirmation .= 'gformRedirect();';
			}
			$confirmation .= '</script>';
		}

		return $confirmation;
	}

	//////////////////////////////////////////////////

	/**
	 * Replace merge tags
	 *
	 * @param string $text
	 * @param array $form
	 * @param array $entry
	 * @param boolean $url_encode
	 * @param boolean $esc_html
	 * @param boolean $nl2br
	 * @param string $format
	 */
	function replace_merge_tags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		$search = array(
			'{payment_status}',
			'{payment_date}',
			'{transaction_id}',
			'{payment_amount}'
		);

		$replace = array(
			rgar( $entry, 'payment_status' ),
			rgar( $entry, 'payment_date' ),
			rgar( $entry, 'transaction_id' ),
			GFCommon::to_money( rgar( $entry, 'payment_amount' ) , rgar( $entry, 'currency' ) )
		);

		if ( $url_encode ) {
			foreach ( $replace as &$value ) {
    			$value = urlencode( $value );
    		}
    	}

    	$text = str_replace( $search, $replace, $text );

		return $text;
	}
}
