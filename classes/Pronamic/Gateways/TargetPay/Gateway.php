<?php

/**
 * Title: TargetPay gateway
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_Gateways_TargetPay_Gateway extends Pronamic_Gateways_Gateway {
	/**
	 * Slug of this gateway
	 * 
	 * @var string
	 */
	const SLUG = 'targetpay';	
	
	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an TargetPay gateway
	 * 
	 * @param Pronamic_Gateways_TargetPay_Config $config
	 */
	public function __construct( Pronamic_Gateways_TargetPay_Config $config ) {
		parent::__construct( $config );

		$this->set_method( Pronamic_Gateways_Gateway::METHOD_HTTP_REDIRECT );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.84 );
		$this->set_slug( self::SLUG );

		$this->client = new Pronamic_Gateways_TargetPay_TargetPay();
	}
	
	/////////////////////////////////////////////////

	/**
	 * Get issuers
	 * 
	 * @see Pronamic_Gateways_Gateway::get_issuers()
	 */
	public function get_issuers() {
		$groups = array();

		$result = $this->client->get_issuers();
		
		if ( $result ) {
			$groups[] = array(
				'options' => $result
			);
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
			'choices'  => $this->get_transient_issuers()
		);
	}
	
	/////////////////////////////////////////////////

	/**
	 * Start
	 * 
	 * @see Pronamic_Gateways_Gateway::start()
	 */
	public function start( Pronamic_Pay_PaymentDataInterface $data, Pronamic_Pay_Payment $payment ) {
		$result = $this->client->start_transaction(
			$this->config->layoutcode,
			$data->get_issuer_id(),
			$data->get_description(),
			$data->get_amount(),
			add_query_arg( 'payment', $payment->get_id(), home_url( '/' ) ),
			add_query_arg( 'payment', $payment->get_id(), home_url( '/' ) )
		);

		if ( $result ) {
			$payment->set_action_url( $result->url );
			$payment->set_transaction_id( $result->transaction_id );
		} else {
			$this->set_error( $this->client->get_error() );
		}
	}
	
	/////////////////////////////////////////////////

	/**
	 * Update status of the specified payment
	 * 
	 * @param Pronamic_Pay_Payment $payment
	 */
	public function update_status( Pronamic_Pay_Payment $payment ) {
		$status = $this->client->check_status(
			$this->config->layoutcode,
			$payment->get_transaction_id(),
			false,
			$this->config->mode == Pronamic_IDeal_IDeal::MODE_TEST
		);

		if ( $status ) {
			$status_text = '';
			
			switch ( $status->code ) {
				case Pronamic_Gateways_TargetPay_ResponseCodes::OK:
					$status_text = Pronamic_Pay_Gateways_IDeal_Statuses::SUCCESS;
					
					$payment->set_consumer_name( $status->account_name );
					$payment->set_consumer_account_number( $status->account_number );
					$payment->set_consumer_city( $status->account_city );
					
					break;
				case Pronamic_Gateways_TargetPay_ResponseCodes::TRANSACTION_NOT_COMPLETED:
					$status_text = Pronamic_Pay_Gateways_IDeal_Statuses::OPEN;

					break;
				case Pronamic_Gateways_TargetPay_ResponseCodes::TRANSACTION_CANCLLED:
					$status_text = Pronamic_Pay_Gateways_IDeal_Statuses::CANCELLED;

					break;
				case Pronamic_Gateways_TargetPay_ResponseCodes::TRANSACTION_EXPIRED:
					$status_text = Pronamic_Pay_Gateways_IDeal_Statuses::EXPIRED;

					break;
				case Pronamic_Gateways_TargetPay_ResponseCodes::TRANSACTION_NOT_PROCESSED:

					break;
				case Pronamic_Gateways_TargetPay_ResponseCodes::ALREADY_USED:

					break;
				case Pronamic_Gateways_TargetPay_ResponseCodes::LAYOUTCODE_NOT_ENTERED:

					break;
				case Pronamic_Gateways_TargetPay_ResponseCodes::TRANSACTION_ID_NOT_ENTERED:

					break;
				case Pronamic_Gateways_TargetPay_ResponseCodes::TRANSACTION_NOT_FOUND:

					break;
				case Pronamic_Gateways_TargetPay_ResponseCodes::LAYOUCODE_NOT_MATCH_TRANSACTION:

					break;
			}
			
			$payment->set_status( $status_text );
		}
	}
}
