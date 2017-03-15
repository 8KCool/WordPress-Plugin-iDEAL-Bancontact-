<?php

/**
 * Title: iDEAL item
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_IDeal_Item {
	/**
	 * The number
	 *
	 * @var string
	 */
	private $number;

	/**
	 * The description
	 *
	 * @var string
	 */
	private $description;

	/**
	 * The quantity
	 *
	 * @var int
	 */
	private $quantity;

	/**
	 * The price
	 *
	 * @var float
	 */
	private $price;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize a iDEAL basic item
	 */
	public function __construct() {
		$this->number      = '';
		$this->description = '';
		$this->quantity    = 1;
		$this->price       = 0;
	}

	//////////////////////////////////////////////////

	/**
	 * Get the number / identifier of this item
	 *
	 * @return string
	 */
	public function getNumber() {
		return $this->number;
	}

	/**
	 * Set the number / identifier of this item
	 *
	 * @param string $number
	 */
	public function setNumber( $number ) {
		$this->number = $number;
	}

	//////////////////////////////////////////////////

	/**
	 * Get the description of this item
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set the description of this item
	 * AN..max32 (AN = Alphanumeric, free text)
	 *
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = substr( $description, 0, 32 );
	}

	//////////////////////////////////////////////////

	/**
	 * Get the quantity of this item
	 *
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantity;
	}

	/**
	 * Set the quantity of this item
	 *
	 * @param int $quantity
	 */
	public function setQuantity( $quantity ) {
		$this->quantity = $quantity;
	}

	//////////////////////////////////////////////////

	/**
	 * Get the price of this item
	 *
	 * @return float
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * Set the price of this item
	 *
	 * @param float $price
	 */
	public function setPrice( $price ) {
		$this->price = $price;
	}

	//////////////////////////////////////////////////

	/**
	 * Get the amount
	 *
	 * @return float
	 */
	public function get_amount() {
		return $this->price * $this->quantity;
	}
}
