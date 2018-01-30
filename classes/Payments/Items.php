<?php

namespace Pronamic\WordPress\Pay\Payments;

use ArrayIterator;
use IteratorAggregate;

/**
 * Title: Items
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0
 */
class Items implements IteratorAggregate {
	/**
	 * The items
	 *
	 * @var array
	 */
	private $items;

	//////////////////////////////////////////////////

	/**
	 * Constructs and initialize a iDEAL basic object
	 */
	public function __construct() {
		$this->items = array();
	}

	//////////////////////////////////////////////////

	/**
	 * Get iterator
	 *
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {
		return new ArrayIterator( $this->items );
	}

	//////////////////////////////////////////////////

	/**
	 * Add item
	 */
	public function addItem( Item $item ) {
		$this->items[] = $item;
	}

	//////////////////////////////////////////////////

	/**
	 * Calculate the total amount of all items
	 */
	public function get_amount() {
		$amount = 0;

		foreach ( $this->items as $item ) {
			$amount += $item->get_amount();
		}

		return $amount;
	}
}
