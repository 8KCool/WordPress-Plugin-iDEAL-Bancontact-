<?php

/**
 * Title: Jigoshop iDEAL gateway
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_Jigoshop_IDeal_IDealGateway extends jigoshop_payment_gateway {
	/**
	 * The unique ID of this payment gateway
	 * 
	 * @var string
	 */
	const ID = 'pronamic_ideal';

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize an iDEAL gateway
	 */
    public function __construct() { 
    	// Parent constructor only exists in Jigoshop 1.3+
    	// @see https://github.com/jigoshop/jigoshop/blob/1.2/gateways/gateway.class.php
    	// @see https://github.com/jigoshop/jigoshop/blob/1.3/gateways/gateway.class.php
    	if ( version_compare( jigoshop::jigoshop_version(), '1.3', '>=' ) ) {
			parent::__construct();
    	}

		// Give this gateway an unique ID so Jigoshop can identiy this gateway
		$this->id              = self::ID;

		// The method title that Jigoshop will display in the admin
		$this->method_title    = __( 'Pronamic iDEAL', 'pronamic_ideal' );
		
		// The icon that Jigoshop will display on the payment methods radio list
		$this->icon            = plugins_url( 'images/icon-24x24.png', Pronamic_WordPress_IDeal_Plugin::$file );

		// Let Jigoshop know that this gateway has field
		// Technically only iDEAL advanced variants has fields
		$this->has_fields      = true;

		// Set default Jigoshop variables, load them form the WordPress options
		$this->enabled         = get_option( 'jigoshop_pronamic_ideal_enabled' );
		$this->title           = get_option( 'jigoshop_pronamic_ideal_title' );
		$this->description     = get_option( 'jigoshop_pronamic_ideal_description' );
		
		// Set own variables, load them form the WordPress options
		$this->configurationId = get_option( 'jigoshop_pronamic_ideal_configuration_id' );

		// Actions
		add_action( 'jigoshop_update_options', array( &$this, 'update_options' ) );
		
		add_action( 'receipt_' . self::ID, array( &$this, 'receipt_page' ) );
    }

	//////////////////////////////////////////////////

	/**
	 * Get default options
	 * 
	 * @return array
	 */	
	protected function get_default_options() {
		$defaults = array();
		
		// Section
		$defaults[] = array( 
			'name' => __( 'Pronamic iDEAL', 'pronamic_ideal' ),
			'type' => 'title',
			'desc' => __( 'Allow iDEAL payments.', 'pronamic_ideal' )
		);

		// Options
		$defaults[] = array(
			'name'		=> __( 'Enable iDEAL', 'pronamic_ideal' ),
			'desc' 		=> '',
			'tip' 		=> '',
			'id' 		=> 'jigoshop_pronamic_ideal_enabled',
			'std' 		=> 'yes',
			'type' 		=> 'checkbox',
			'choices'	=> array(
				'no'			=> __( 'No', 'pronamic_ideal' ),
				'yes'			=> __( 'Yes', 'pronamic_ideal' )
			)
		);

		$defaults[] = array(
			'name'		=> __( 'Title', 'pronamic_ideal' ),
			'desc' 		=> '',
			'tip' 		=> __( 'This controls the title which the user sees during checkout.', 'pronamic_ideal' ),
			'id' 		=> 'jigoshop_pronamic_ideal_title',
			'std' 		=> __( 'iDEAL', 'pronamic_ideal' ),
			'type' 		=> 'text'
		);
		
		$defaults[] = array(
			'name'		=> __( 'Description', 'pronamic_ideal' ),
			'desc' 		=> '',
			'tip' 		=> __( 'This controls the description which the user sees during checkout.', 'pronamic_ideal' ),
			'id' 		=> 'jigoshop_pronamic_ideal_description',
			'std' 		=> '',
			'type' 		=> 'longtext'
		);
		
		$defaults[] = array(
			'name'		=> __( 'Configuration', 'pronamic_ideal' ),
			'desc' 		=> '',
			'tip' 		=> '',
			'id' 		=> 'jigoshop_pronamic_ideal_configuration_id',
			'std' 		=> '',
			'type' 		=> 'select',
			'choices'	=> Pronamic_WordPress_IDeal_IDeal::get_configurations_select_options() 
		);

		return $defaults;
	}

	//////////////////////////////////////////////////

    /**
	 * Payment fields
	 */
	function payment_fields() {
		if ( ! empty( $this->description ) ) {
			echo wpautop( wptexturize( $this->description ) );
		}

		$configuration = Pronamic_WordPress_IDeal_ConfigurationsRepository::getConfigurationById( $this->configurationId );
		
		$gateway = Pronamic_WordPress_IDeal_IDeal::get_gateway( $configuration );

		if ( $gateway ) {
			echo $gateway->get_input_html();
		}
	}

	//////////////////////////////////////////////////
	
	/**
	 * Receipt page
	 */
	function receipt_page( $order_id ) {
		$configuration = Pronamic_WordPress_IDeal_ConfigurationsRepository::getConfigurationById( $this->configurationId );
		
		$order = &new jigoshop_order( $order_id );
		
		$data = new Pronamic_Jigoshop_IDeal_IDealDataProxy( $order );

		$gateway = Pronamic_WordPress_IDeal_IDeal::get_gateway( $configuration );

		if ( $gateway ) {
			Pronamic_WordPress_IDeal_IDeal::start( $configuration, $gateway, $data );

			echo $gateway->get_form_html();
		}
	}

	//////////////////////////////////////////////////
	
	/**
	 * Process the payment and return the result
	 */
	function process_payment( $order_id ) {
		$order = &new jigoshop_order( $order_id );

		// Mark as on-hold (we're awaiting the payment)
		$order->update_status( 'pending', __( 'Pending iDEAL payment.', 'pronamic_ideal' ) );

		// Empty cart
		jigoshop_cart::empty_cart();

		// Do specifiek iDEAL variant processing
		$configuration = Pronamic_WordPress_IDeal_ConfigurationsRepository::getConfigurationById( $this->configurationId );

		$gateway = Pronamic_WordPress_IDeal_IDeal::get_gateway( $configuration );
		
		if ( $gateway ) {
			if ( $gateway->is_http_redirect() ) {
				$return = $this->process_gateway_http_redirect( $order, $configuration, $gateway );
			}

			if ( $gateway->is_html_form() ) {
				$return = $this->process_gateway_html_form( $order );
			}
			
			if ( ! $gateway->has_feedback() ) {
				
			}
		}
		
		return $return;
    }

	private function process_gateway_html_form( $order ) {
		// Return pay page redirect
		return array(
			'result' 	=> 'success',
			'redirect'	=> add_query_arg(
				array(
					'order' => $order->id,
					'key'   => $order->order_key
				), 
				get_permalink( jigoshop_get_page_id( 'pay' ) ) 
			)
		);
	}

    private function process_gateway_http_redirect( $order, $configuration, $gateway ) {
		$data = new Pronamic_Jigoshop_IDeal_IDealDataProxy( $order );

		Pronamic_WordPress_IDeal_IDeal::start( $configuration, $gateway, $data );

		$error = $gateway->get_error();

		if ( is_wp_error( $error ) ) {
			jigoshop::add_error( Pronamic_WordPress_IDeal_IDeal::get_default_error_message() );

			// see https://github.com/jigoshop/jigoshop/blob/1.4.9/shortcodes/pay.php#L55
			return array(
				'result' 	=> 'failed'
			);
		} else {
	    	$url = $gateway->get_action_url();
	
			return array(
				'result' 	=> 'success',
				'redirect'	=> $url
			);
		}
    }
}
