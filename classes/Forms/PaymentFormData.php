<?php
/**
 * Payment Form Data
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Forms
 */

namespace Pronamic\WordPress\Pay\Forms;

use Pronamic\WordPress\Pay\Core\Util as Core_util;
use Pronamic\WordPress\Pay\Payments\Item;
use Pronamic\WordPress\Pay\Payments\Items;
use \Pronamic\WordPress\Pay\Payments\PaymentData;

/**
 * Payment Form Data
 *
 * @author Remco Tolsma
 * @version 4.5.3
 * @since 3.7.0
 */
class PaymentFormData extends PaymentData {
	/**
	 * Get source indicator.
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_source()
	 * @return string
	 */
	public function get_source() {
		return 'payment_form';
	}

	/**
	 * Get source ID.
	 *
	 * @return string
	 */
	public function get_source_id() {
		return filter_input( INPUT_POST, 'pronamic_pay_form_id', FILTER_VALIDATE_INT );
	}

	/**
	 * Get description.
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_description()
	 * @return string
	 */
	public function get_description() {
		return sprintf( __( 'Payment Form %s', 'pronamic_ideal' ), $this->get_order_id() );
	}

	/**
	 * Get order ID.
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_order_id()
	 * @return string
	 */
	public function get_order_id() {
		return time();
	}

	/**
	 * Get items.
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_items()
	 * @return Items
	 */
	public function get_items() {
		// Items.
		$items = new Items();

		// Amount.
		$amount_method = get_post_meta( $this->get_source_id(), '_pronamic_payment_form_amount_method', true );
		$amount        = filter_input( INPUT_POST, 'pronamic_pay_amount', FILTER_SANITIZE_STRING );

		if ( 'other' === $amount ) {
			$amount = filter_input( INPUT_POST, 'pronamic_pay_amount_other', FILTER_SANITIZE_STRING );

			$amount = Core_util::string_to_amount( $amount );
		} elseif ( in_array( $amount_method, array( FormPostType::AMOUNT_METHOD_CHOICES_ONLY, FormPostType::AMOUNT_METHOD_CHOICES_AND_INPUT ), true ) ) {
			$amount /= 100;
		}

		// Item.
		$item = new Item();
		$item->setNumber( $this->get_order_id() );
		$item->setDescription( sprintf( __( 'Payment %s', 'pronamic_ideal' ), $this->get_order_id() ) );
		$item->setPrice( $amount );
		$item->setQuantity( 1 );

		$items->addItem( $item );

		return $items;
	}

	/**
	 * Get currency alphabetic code.
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_currency_alphabetic_code()
	 * @return string
	 */
	public function get_currency_alphabetic_code() {
		return 'EUR';
	}

	/**
	 * Get email.
	 *
	 * @return string
	 */
	public function get_email() {
		return filter_input( INPUT_POST, 'pronamic_pay_email', FILTER_SANITIZE_EMAIL );
	}

	/**
	 * Get customer name.
	 *
	 * @return string
	 */
	public function get_customer_name() {
		$first_name = filter_input( INPUT_POST, 'pronamic_pay_first_name', FILTER_SANITIZE_STRING );
		$last_name  = filter_input( INPUT_POST, 'pronamic_pay_last_name', FILTER_SANITIZE_STRING );

		return $first_name . ' ' . $last_name;
	}

	/**
	 * Get address.
	 *
	 * @return string
	 */
	public function get_address() {
		return '';
	}

	/**
	 * Get city.
	 *
	 * @return string
	 */
	public function get_city() {
		return '';
	}

	/**
	 * Get ZIP.
	 *
	 * @return string
	 */
	public function get_zip() {
		return '';
	}
}
