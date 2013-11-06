<?php

/**
 * Title: OmniKassa listener
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_Gateways_OmniKassa_Listener implements Pronamic_Pay_Gateways_ListenerInterface {
	public static function listen() {
		$condition  = true;
		$condition &= filter_has_var( INPUT_POST, 'Data' );
		$condition &= filter_has_var( INPUT_POST, 'Seal' );
		
		if ( $condition ) {
			$input_data = filter_input( INPUT_POST, 'Data', FILTER_SANITIZE_STRING );
			$input_seal = filter_input( INPUT_POST, 'Seal', FILTER_SANITIZE_STRING );
			
			$data = Pronamic_Gateways_OmniKassa_OmniKassa::parse_piped_string( $input_data );
			
			$transaction_reference = $data['transactionReference'];
			
			$payment = get_pronamic_payment_by_transaction_id( $transaction_reference );

			Pronamic_WordPress_IDeal_Plugin::update_payment( $payment );
		}
	}
}
