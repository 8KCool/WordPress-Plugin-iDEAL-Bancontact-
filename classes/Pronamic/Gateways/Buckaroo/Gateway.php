<?php

/**
 * Title: Buckaroo gateway
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_Gateways_Buckaroo_Gateway extends Pronamic_Gateways_Gateway {
	/**
	 * Slug of this gateway
	 * 
	 * @var string
	 */
	const SLUG = 'buckaroo';

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an InternetKassa gateway
	 * 
	 * @param Pronamic_WordPress_IDeal_Configuration $configuration
	 */
	public function __construct( Pronamic_WordPress_IDeal_Configuration $configuration ) {
		parent::__construct( $configuration );

		$this->set_method( Pronamic_Gateways_Gateway::METHOD_HTML_FORM );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.01 );
		$this->set_slug( self::SLUG );

		$this->client = new Pronamic_Gateways_Buckaroo_Buckaroo();
		$this->client->set_payment_server_url( $configuration->getPaymentServerUrl() );
		$this->client->set_website_key( $configuration->buckarooWebsiteKey );
		$this->client->set_secret_key( $configuration->buckarooSecretKey );
	}

	/////////////////////////////////////////////////

	/**
	 * Start
	 * 
	 * @param Pronamic_Pay_PaymentDataInterface $data
	 * @see Pronamic_Gateways_Gateway::start()
	 */
	public function start( Pronamic_Pay_PaymentDataInterface $data ) {
		$this->set_transaction_id( md5( time() . $data->getOrderId() ) );
		$this->set_action_url( $this->client->get_payment_server_url() );

		// Buckaroo uses 'nl-NL' instead of 'nl_NL'
		$culture = str_replace( '_', '-', $data->getLanguageIso639AndCountryIso3166Code() );

		$this->client->set_culture( $culture );
		$this->client->set_currency( $data->getCurrencyAlphabeticCode() );
		$this->client->set_invoice_number( $data->getOrderId() );
		$this->client->set_description( $data->getDescription() );
		$this->client->set_amount( $data->getAmount() );
		
		$return_url = add_query_arg( 'gateway', 'buckaroo', home_url( '/' ) );

		$this->client->set_return_url( $return_url );
		$this->client->set_return_cancel_url( $return_url );
		$this->client->set_return_error_url( $return_url );
		$this->client->set_return_reject_url( $return_url );
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
