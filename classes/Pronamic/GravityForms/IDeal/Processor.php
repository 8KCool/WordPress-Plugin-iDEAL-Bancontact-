<?php

/**
 * Title: Processor
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_GravityForms_IDeal_Processor {
	/**
	 * The Gravity Forms form
	 * 
	 * @var array
	 */
	private $form;

	/**
	 * The Gravity Forms form ID
	 * 
	 * @var string
	 */
	private $form_id;

	//////////////////////////////////////////////////

	/**
	 * Process flag
	 * 
	 * @var boolean
	 */
	private $process;

	//////////////////////////////////////////////////

	/**
	 * Payment feed
	 * 
	 * @var Pronamic_GravityForms_PayFeed
	 */
	private $feed;

	/**
	 * Gateway
	 * 
	 * @var Pronamic_WP_Pay_Payment
	 */
	private $gateway;

	/**
	 * Payment
	 * 
	 * @var Pronamic_WP_Pay_Payment
	 */
	private $payment;

	//////////////////////////////////////////////////

	/**
	 * Error
	 * 
	 * @var WP_Error
	 */
	private $error;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initalize an Gravity Forms payement form processor
	 * 
	 * @param array $form
	 */
	public function __construct( array $form ) {
		$this->form    = $form;
		$this->form_id = isset( $form['id'] ) ? $form['id'] : null;

		// Get payment feed by form ID
		$this->feed = get_pronamic_gf_pay_feed_by_form_id( $form['id'] );

		if ( null != $this->feed ) {
			if ( Pronamic_GravityForms_IDeal_AddOn::is_condition_true( $this->form, $this->feed ) ) {
				$this->process = true;
				
				$this->add_hooks();
			}
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Add hooks
	 */
	private function add_hooks() {
		/*
		 * Pre submission
		 */
		add_action( 'gform_pre_submission_' . $this->form_id, array( $this, 'pre_submission' ), 10, 1 );
		
		/*
		 * Handle submission
		 */
		// Lead
		add_action( 'gform_entry_created', array( $this, 'entry_created' ), 10, 2 );
		add_action( 'gform_entry_post_save', array( $this, 'entry_post_save' ), 10, 2 );

		// Delay (@see GFFormDisplay::handle_submission > GFCommon::send_form_submission_notifications)
		add_filter( 'gform_disable_admin_notification_' . $this->form_id, array( $this, 'maybe_delay_admin_notification' ), 10, 3 );
		add_filter( 'gform_disable_user_notification_' . $this->form_id,  array( $this, 'maybe_delay_user_notification' ), 10, 3 );
		add_filter( 'gform_disable_post_creation_' . $this->form_id, array( $this, 'maybe_delay_post_creation' ), 10, 3 );
		add_filter( 'gform_disable_notification_' . $this->form_id, array( $this, 'maybe_delay_notification' ), 10, 4 );

		// Confirmation (@see GFFormDisplay::handle_confirmation)
		// @see http://www.gravityhelp.com/documentation/page/Gform_confirmation
		add_filter( 'gform_confirmation_' . $this->form_id, array( $this, 'confirmation' ), 10, 4 );

		/*
		 * After submission
		 */
		add_action( 'gform_after_submission_' . $this->form_id, array( $this, 'after_submission' ), 10, 2 );
		
		// Delay
		add_action( 'gform_after_submission_' . $this->form_id, array( $this, 'maybe_delay_campaignmonitor_subscription' ), 1, 2 );
		add_action( 'gform_after_submission_' . $this->form_id, array( $this, 'maybe_delay_mailchimp_subscription' ), 1, 2 );
	}

	//////////////////////////////////////////////////

	/**
	 * Pre submission
	 */
	public function pre_submission( $form ) {
		if ( $this->process ) {
			
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Entry created
	 * 
	 * @param array $lead
	 * @param array $form
	 */
	public function entry_created( $lead, $form ) {
		if ( $this->process && $this->form == $form ) {
			$this->gateway = Pronamic_WordPress_IDeal_IDeal::get_gateway( $this->feed->config_id );

			if ( $this->gateway ) {
				$data = new Pronamic_WP_Pay_GravityForms_PaymentData( $form, $lead, $this->feed );
			
				$this->payment = Pronamic_WordPress_IDeal_IDeal::start( $this->feed->config_id, $this->gateway, $data );
			
				$this->error = $this->gateway->get_error();
			}
		}
	}

	/**
	 * Entry post save
	 * 
	 * @param array $lead
	 * @param array $form
	 */
	public function entry_post_save( $lead, $form ) {
		if ( $this->process && $this->form == $form ) {
			// Updating lead's payment_status to Processing
			$lead[Pronamic_GravityForms_LeadProperties::PAYMENT_STATUS]   = Pronamic_GravityForms_PaymentStatuses::PROCESSING;
			$lead[Pronamic_GravityForms_LeadProperties::PAYMENT_AMOUNT]   = $this->payment->get_amount();
			$lead[Pronamic_GravityForms_LeadProperties::PAYMENT_DATE]     = gmdate( 'y-m-d H:i:s' );
			$lead[Pronamic_GravityForms_LeadProperties::TRANSACTION_TYPE] = Pronamic_GravityForms_GravityForms::TRANSACTION_TYPE_PAYMENT;
			$lead[Pronamic_GravityForms_LeadProperties::TRANSACTION_ID]   = $this->payment->get_transaction_id();

			// Update entry meta with payment ID
			gform_update_meta( $lead['id'], 'pronamic_payment_id', $this->payment->get_id() );

			// Update entry meta with feed ID
			gform_update_meta( $lead['id'], 'ideal_feed_id', $feed->id );
			
			// Update entry meta with current payment gateway
			gform_update_meta( $lead['id'], 'payment_gateway', 'ideal' );

			// Update lead
			RGFormsModel::update_lead( $lead );
		}

		return $lead;
	}

	//////////////////////////////////////////////////
	// Delay functions
	//////////////////////////////////////////////////
	
	public function maybe_delay_notification( $is_disabled, $notification, $form, $lead ) {
		$is_disabled = false;
	
		if ( $this->process ) {
			$notification_ids = $this->feed->delay_notification_ids;

			$is_disabled = in_array( $notification['id'], $notification_ids );
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
	public function maybe_delay_admin_notification( $is_disabled, $form, $lead ) {
		$is_disabled = false;
	
		if ( $this->process ) {
			$is_disabled = $this->feed->delay_admin_notification;
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
	public function maybe_delay_user_notification( $is_disabled, $form, $lead ) {
		$is_disabled = false;
	
		if ( $this->process ) {
			$is_disabled = $this->feed->delay_user_notification;
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
	public function maybe_delay_post_creation( $is_disabled, $form, $lead ) {
		$is_disabled = false;
	
		if ( $this->process ) {
			$is_disabled = $feed->delay_post_creation;
		}
	
		return $is_disabled;
	}
	
	//////////////////////////////////////////////////
	
	/**
	 * Maybe delay Campaign Monitor subscription
	 */
	public function maybe_delay_campaignmonitor_subscription( $lead, $form ) {
		if ( $this->process ) {
			if ( $this->feed->delay_campaignmonitor_subscription ) {
				remove_action( 'gform_after_submission', array( 'GFCampaignMonitor', 'export' ), 10, 2);
			}
		}
	}
	
	/**
	 * Maybe delay MailChimp subscription
	 */
	public function maybe_delay_mailchimp_subscription( $lead, $form ) {
		if ( $this->process ) {
			if ( $this->feed->delay_mailchimp_subscription ) {
				remove_action( 'gform_after_submission', array( 'GFMailChimp', 'export' ), 10, 2);
			}
		}
	}

	//////////////////////////////////////////////////
	// Confirmation
	//////////////////////////////////////////////////

	/**
	 * Confirmation
	 *
	 * @see http://www.gravityhelp.com/documentation/page/Gform_confirmation
	 */
	public function confirmation( $confirmation, $form, $lead, $ajax ) {
		if ( $this->process && $this->gateway && $this->payment ) {
			if ( is_wp_error( $this->error ) ) {
				$html  = '';

				$html .= '<ul>';
				$html .= '<li>' . Pronamic_WordPress_IDeal_IDeal::get_default_error_message() . '</li>';
		
				foreach ( $this->error->get_error_messages() As $message ) {
					$html .= '<li>' . $message . '</li>';
				}
					
				$html .= '</ul>';
					
				$confirmation = $html;
			} else {	
				if ( $this->gateway->is_http_redirect() ) {
					// Redirect user to the issuer
					$confirmation = array( 'redirect' => $this->payment->get_action_url() );
				}
		
				if ( $this->gateway->is_html_form() ) {
					$auto_submit = true;
					if ( $ajax ) {
						// On AJAX enabled forms we can't auto submit, this will auto submit in a hidden iframe
						$auto_submit = false;
					}

					// HTML
					$html  = '';
					$html .= '<div id="gforms_confirmation_message">';
					$html .= 	GFCommon::replace_variables( $form['confirmation']['message'], $form, $lead, false, true, true );
					$html .= 	$this->gateway->get_form_html( $this->payment, $auto_submit );
					$html .= '</div>';

					// Extend the confirmation with the iDEAL form
					$confirmation = $html;
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
		}
		
		return $confirmation;
	}

	//////////////////////////////////////////////////

	/**
	 * After submission
	 */
	public function after_submission( $lead, $form ) {
		if ( $this->process ) {
			
		}
	}
}
