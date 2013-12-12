<?php

/**
 * Title: Jigoshop iDEAL data proxy
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_WP_Pay_Jigoshop_PaymentData extends Pronamic_WP_Pay_PaymentData {
	/**
	 * Order
	 * 
	 * @see plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/classes/jigoshop_order.class.php
	 * @var jigoshop_order
	 */
	private $order;

	//////////////////////////////////////////////////

	/**
	 * Construct and intializes an Jigoshop iDEAL data proxy
	 * 
	 * @param jigoshop_order $order
	 */
	public function __construct( $order ) {
		parent::__construct();

		$this->order = $order;
	}

	//////////////////////////////////////////////////

	/**
	 * Get source indicatir
	 * 
	 * @see Pronamic_Pay_PaymentDataInterface::get_source()
	 * @return string
	 */
	public function get_source() {
		return 'jigoshop';
	}

	//////////////////////////////////////////////////

	/**
	 * Get description
	 * 
	 * @see Pronamic_Pay_PaymentDataInterface::get_description()
	 * @return string
	 */
	public function get_description() {
		// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/classes/jigoshop_order.class.php#L50
		return sprintf( __( 'Order %s', 'pronamic_ideal' ), $this->order->id );
	}

	/**
	 * Get order ID
	 * 
	 * @see Pronamic_Pay_PaymentDataInterface::get_order_id()
	 * @return string
	 */
	public function get_order_id() {
		// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/classes/jigoshop_order.class.php#L50
		return $this->order->id;
	}

	/**
	 * Get items
	 * 
	 * @see Pronamic_Pay_PaymentDataInterface::getItems()
	 * @return Pronamic_IDeal_Items
	 */
	public function getItems() {
		// Items
		$items = new Pronamic_IDeal_Items();

		// Item
		// We only add one total item, because iDEAL cant work with negative price items (discount)
		$item = new Pronamic_IDeal_Item();
		$item->setNumber( $this->order->id );
		$item->setDescription( sprintf( __( 'Order %s', 'pronamic_ideal' ), $this->order->id ) );
		// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/classes/jigoshop_order.class.php#L98
		// @see https://github.com/jigoshop/jigoshop/blob/dev/classes/jigoshop_order.class.php#L124
		$item->setPrice( $this->order->order_total );
		$item->setQuantity( 1 );

		$items->addItem( $item );

		return $items;
	}

	//////////////////////////////////////////////////
	// Currency
	//////////////////////////////////////////////////

	public function get_currency_alphabetic_code() {
		// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/admin/jigoshop-admin-settings-options.php#L421
		return get_option( 'jigoshop_currency' );
	}

	//////////////////////////////////////////////////
	// Customer
	//////////////////////////////////////////////////

	public function get_email() {
		// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/classes/jigoshop_order.class.php#L71
		return $this->order->billing_email;
	}

	public function getCustomerName() {
		// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/classes/jigoshop_order.class.php#L62
		return $this->order->billing_first_name . ' ' . $this->order->billing_last_name;
	}

	public function getOwnerAddress() {
		// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/classes/jigoshop_order.class.php#L65
		return $this->order->billing_address_1;
	}

	public function getOwnerCity() {
		// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/classes/jigoshop_order.class.php#L67
		return $this->order->billing_city;
	}

	public function getOwnerZip() {
		// http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/classes/jigoshop_order.class.php#L68
		return $this->order->billing_postcode;
	}

	//////////////////////////////////////////////////
	// URL's
	//////////////////////////////////////////////////
	
	public function get_normal_return_url() {
		return add_query_arg(
			array(
				'key'   => $this->order->order_key,
				'order' => $this->order->id
			) , 
			// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/jigoshop.php#L442
			get_permalink( jigoshop_get_page_id( 'view_order' ) )
		);
	}
	
	public function get_cancel_url() {
		// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/classes/jigoshop_order.class.php#L320
		return $this->order->get_cancel_order_url();
	}
	
	public function get_success_url() {
		return add_query_arg(
			array(
				'key'   => $this->order->order_key,
				'order' => $this->order->id
			) , 
			// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/jigoshop.php#L442
			get_permalink( jigoshop_get_page_id( 'thanks' ) )
		);
	}

	public function get_error_url() {
		// @see http://plugins.trac.wordpress.org/browser/jigoshop/tags/1.1.1/classes/jigoshop_order.class.php#L309
		return $this->order->get_checkout_payment_url();
	}
}
