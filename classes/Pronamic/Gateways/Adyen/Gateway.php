<?php

/**
 * Title: Adyen gateway
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_Gateways_Adyen_Gateway extends Pronamic_Gateways_Gateway {
	/**
	 * Slug of this gateway
	 * 
	 * @var string
	 */
	const SLUG = 'adyen';

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an InternetKassa gateway
	 * 
	 * @param Pronamic_Pay_Config $config
	 */
	public function __construct( Pronamic_Pay_Config $config ) {
		parent::__construct( $config );

		$this->set_method( Pronamic_Gateways_Gateway::METHOD_HTML_FORM );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.01 );
		$this->set_slug( self::SLUG );

		$this->client = new Pronamic_Gateways_Adyen_Adyen();
		$this->client->set_payment_server_url( $config->getPaymentServerUrl() );
		$this->client->set_skin_code( $config->get_buckaroo_skin_code() );
		$this->client->set_merchant_account( $config->get_buckaroo_merchant_account() );
		$this->client->set_shared_secret( $config->get_buckaroo_shared_secret() );
	}

	/////////////////////////////////////////////////

	/**
	 * Start
	 * 
	 * @param Pronamic_Pay_PaymentDataInterface $data
	 * @see Pronamic_Gateways_Gateway::start()
	 */
	public function start( Pronamic_Pay_PaymentDataInterface $data, Pronamic_Pay_Payment $payment ) {
		$payment->set_transaction_id( md5( time() . $data->get_order_id() ) );
		$payment->set_action_url( $this->client->get_payment_server_url() );

		$this->client->set_merchant_reference( $data->get_order_id() );
		$this->client->set_payment_amount( $data->get_amount() );
		$this->client->set_currency_code( $data->get_currency() );
		$this->client->set_ship_before_date( new DateTime( '+5 days' ) );
		$this->client->set_shopper_locale( $data->get_language_and_country() );
		$this->client->set_order_data( $data->get_description() );
		$this->client->set_session_validity( new DateTime ( '+1 hour' ) );
		$this->client->set_shopper_reference( $data->get_email() );
		$this->client->set_shopper_email( $data->get_email() );
	}
	
	/////////////////////////////////////////////////

	/**
	 * Get output HTML
	 * 
	 * @see Pronamic_Gateways_Gateway::get_output_html()
	 */
	public function get_output_html() {
		return $this->client->get_html_fields();
	}
}
