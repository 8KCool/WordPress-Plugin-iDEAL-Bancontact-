<?php

/**
 * Title: iDEAL client
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_Gateways_IDealAdvancedV3_Client {
	public $acquirer_url;
	
	//////////////////////////////////////////////////
	
	public $directory_request_url;

	public $transaction_request_url;
	
	public $status_request_url;
	
	//////////////////////////////////////////////////

	public $merchant_id;
	
	public $sub_id;
	
	//////////////////////////////////////////////////
	
	public $private_key_password;
	
	public $private_key;
	
	public $private_certificate;
	
	//////////////////////////////////////////////////

	private $error;
	
	//////////////////////////////////////////////////

	public function __construct() {

	}
	
	//////////////////////////////////////////////////

	public function set_acquirer_url( $url ) {
		$this->acquirer_url            = $url;

		$this->directory_request_url   = $url;
		$this->transaction_request_url = $url;
		$this->status_request_url      = $url;
	}
	
	//////////////////////////////////////////////////

	/**
	 * Send an message
	 */
	private function send_message( $url, Pronamic_Gateways_IDealAdvancedV3_XML_RequestMessage $message ) {
		$result = false;

		// Sign
		$document = $message->get_document();
		$document = $this->sign_document( $document );
		
		// Stringify
		$data = $document->saveXML();

		// Remote post
		$response = wp_remote_post( $url, array(
			'method'    => 'POST',
			'headers'   => array(
				'Content-Type' => 'text/xml; charset=' . Pronamic_Gateways_IDealAdvancedV3_XML_Message::XML_ENCODING
			),
			'sslverify' => false,
			'body'      => $data
		) );

		// Handle response
		if ( ! is_wp_error( $response ) ) {
			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$body = wp_remote_retrieve_body( $response );

				// Suppress all XML errors
				$use_errors = libxml_use_internal_errors( true );

				$document = simplexml_load_string( $body );

				if ( $document !== false ) {
					$result = $this->parse_document( $document );
				} else {
					$this->error = new WP_Error( 'xml_load_error', __( 'Could not load the XML response meessage from the iDEAL provider.', 'pronamic_ideal' ) );

					foreach ( libxml_get_errors() as $error ) {
						$this->error->add( 'libxml_error', $error->message, $error );
					}

					libxml_clear_errors();
				}
				
				// Set back to previous value 
				libxml_use_internal_errors( $use_errors );
			} else {
				$this->error = new WP_Error( 'wrong_response_code', __( 'The response code (<code>%s<code>) from the iDEAL provider was incorrect.', 'pronamic_ideal' ) );
			}
		} else {
			$this->error = $response;
		}
		
		return $result;
	}

	//////////////////////////////////////////////////

	/**
	 * Parse the specified document and return parsed result
	 * 
	 * @param SimpleXMLElement $document
	 */
	private function parse_document( SimpleXMLElement $document ) {
		$this->error = null;

		switch( $document->getName() ) {
			case Pronamic_Gateways_IDealAdvancedV3_XML_AcquirerErrorResMessage::NAME:
				$message = Pronamic_Gateways_IDealAdvancedV3_XML_AcquirerErrorResMessage::parse( $document );

				$this->error = new WP_Error( 'ideal_advanced_v3_error', $message->error, $message );

				return $message;
			case Pronamic_Gateways_IDealAdvancedV3_XML_DirectoryResponseMessage::NAME:
				return Pronamic_Gateways_IDealAdvancedV3_XML_DirectoryResponseMessage::parse( $document );
			case Pronamic_Gateways_IDealAdvancedV3_XML_TransactionResponseMessage::NAME:
				return Pronamic_Gateways_IDealAdvancedV3_XML_TransactionResponseMessage::parse( $document );
			case Pronamic_Gateways_IDealAdvancedV3_XML_AcquirerStatusResMessage::NAME:
				return Pronamic_Gateways_IDealAdvancedV3_XML_AcquirerStatusResMessage::parse( $document );
			default:
				return null;
		}
	}

	public function get_directory() {
		$directory = false;

		$request_dir_message = new Pronamic_Gateways_IDealAdvancedV3_XML_DirectoryRequestMessage();

		$merchant = $request_dir_message->get_merchant();
		$merchant->set_id( $this->merchant_id );
		$merchant->set_sub_id( $this->sub_id );

		$response_dir_message = $this->send_message( $this->acquirer_url, $request_dir_message );
		
		if ( $response_dir_message instanceof Pronamic_Gateways_IDealAdvancedV3_XML_DirectoryResponseMessage ) {
			$directory = $response_dir_message->get_directory();
		}

		return $directory;
	}

	public function create_transaction( Pronamic_Gateways_IDealAdvancedV3_Transaction $transaction, $issuer_id ) {
		$message = new Pronamic_Gateways_IDealAdvancedV3_XML_TransactionRequestMessage();

		$merchant = $message->get_merchant();
		$merchant->set_id( $this->merchant_id );
		$merchant->set_sub_id( $this->sub_id );
		$merchant->set_return_url( site_url( '/' ) ); 

		$message->issuer = new Pronamic_Gateways_IDealAdvancedV3_Issuer();
		$message->issuer->set_id( $issuer_id );
		
		$message->transaction = $transaction;

		return $this->send_message( $this->transaction_request_url, $message );
	}

	public function get_status( $transaction_id ) {
		$message = new Pronamic_Gateways_IDealAdvancedV3_XML_AcquirerStatusReqMessage();

		$merchant = $message->get_merchant();
		$merchant->set_id( $this->merchant_id );
		$merchant->set_sub_id( $this->sub_id );

		$message->transaction = new Pronamic_Gateways_IDealAdvancedV3_Transaction();
		$message->transaction->set_id( $transaction_id );

		return $this->send_message( $this->status_request_url, $message );
	}
	
	public function get_error() {
		return $this->error;
	}
	
	private function sign_document( DOMDocument $document ) {
		if ( empty( $this->private_key ) || empty( $this->private_key_password ) || empty( $this->private_certificate ) ) {
			// @todo what todo?
			// can't sign document
		} else {
			$dsig = new XMLSecurityDSig();
			$dsig->setCanonicalMethod( XMLSecurityDSig::EXC_C14N );
			$dsig->addReference( 
				$document,
				XMLSecurityDSig::SHA256,
				array( 'http://www.w3.org/2000/09/xmldsig#enveloped-signature' ),
				array( 'force_uri' => true )
			);
			
			$key = new XMLSecurityKey( XMLSecurityKey::RSA_SHA256, array( 'type' => 'private' ) );
			$key->passphrase = $this->private_key_password;
			$key->loadKey( $this->private_key );
			
			$dsig->sign( $key );
			
			$fingerprint = Pronamic_Gateways_IDealAdvanced_Security::getShaFingerprint( $this->private_certificate );
			
			$dsig->addKeyInfoAndName( $fingerprint );
			$dsig->appendSignature( $document->documentElement );
		}

		return $document;
	}
}
