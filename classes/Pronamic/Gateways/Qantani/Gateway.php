<?php

/**
 * Title: Qantani gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_Gateways_Qantani_Gateway extends Pronamic_Gateways_Gateway {
	/**
	 * Slug of this gateway
	 *
	 * @var string
	 */
	const SLUG = 'qantani';

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an Qantani gateway
	 *
	 * @param Pronamic_Gateways_Qantani_Config $config
	 */
	public function __construct( Pronamic_Gateways_Qantani_Config $config ) {
		parent::__construct( $config );

		$this->set_method( Pronamic_Gateways_Gateway::METHOD_HTTP_REDIRECT );
		$this->set_has_feedback( false );
		$this->set_amount_minimum( 1.20 );
		$this->set_slug( self::SLUG );

		$this->client = new Pronamic_Gateways_Qantani_Qantani();
		$this->client->set_merchant_id( $config->merchant_id );
		$this->client->set_merchant_key( $config->merchant_key );
		$this->client->set_merchant_secret( $config->merchant_secret );
	}

	/////////////////////////////////////////////////

	/**
	 * Get issuers
	 *
	 * @see Pronamic_Gateways_Gateway::get_issuers()
	 */
	public function get_issuers() {
		$groups = array();

		$result = $this->client->get_banks();

		if ( $result ) {
			$groups[] = array(
				'options' => $result
			);
		} else {
			$this->error = $this->client->get_error();
		}

		return $groups;
	}

	/////////////////////////////////////////////////

	public function get_issuer_field() {
		return array(
			'id'       => 'pronamic_ideal_issuer_id',
			'name'     => 'pronamic_ideal_issuer_id',
			'label'    => __( 'Choose your bank', 'pronamic_ideal' ),
			'required' => true,
			'type'     => 'select',
			'choices'  => $this->get_issuers()
		);
	}

	/////////////////////////////////////////////////

	/**
	 * Start
	 *
	 * @param Pronamic_Pay_PaymentDataInterface $data
	 * @see Pronamic_Gateways_Gateway::start()
	 */
	public function start( Pronamic_Pay_PaymentDataInterface $data, Pronamic_Pay_Payment $payment ) {
		$result = $this->client->create_transaction(
			$data->get_amount(),
			$data->get_currency(),
			$data->get_issuer_id(),
			$data->get_description(),
			add_query_arg( 'payment', $payment->get_id(), home_url( '/' ) )
		);

		if ( $result !== false ) {
			$payment->set_transaction_id( $result->transaction_id );
			$payment->set_action_url( $result->bank_url );
		} else {
			$this->error = $this->client->get_error();
		}
	}

	/////////////////////////////////////////////////

	/**
	 * Update status of the specified payment
	 *
	 * @param Pronamic_Pay_Payment $payment
	 */
	public function update_status( Pronamic_Pay_Payment $payment ) {
		$transaction_id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING );
		$status         = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_STRING );
		$salt           = filter_input( INPUT_GET, 'salt', FILTER_SANITIZE_STRING );
		$checksum       = filter_input( INPUT_GET, 'checksum', FILTER_SANITIZE_STRING );
		
		switch ( $status ) {
			case Pronamic_Gateways_Qantani_Qantani::PAYMENT_STATUS_PAID:
				$payment->set_status( Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_SUCCESS );
				
				break;
			case Pronamic_Gateways_Qantani_Qantani::PAYMENT_STATUS_CANCELLED:
				$payment->set_status( Pronamic_Gateways_IDealAdvanced_Transaction::STATUS_CANCELLED );
				
				break;
		}
	}
}
