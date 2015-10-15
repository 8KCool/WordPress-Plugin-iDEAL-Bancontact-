<?php

/**
 * Execute changes made in Pronamic Pay 2.0.1
 *
 * @see https://github.com/WordPress/WordPress/blob/3.5.1/wp-admin/includes/upgrade.php#L413
 * @since 2.1.0
 */

global $wpdb;

/*

-- You can undo the database upgrade by executing the following queries

DELETE FROM wp_options WHERE option_name = 'pronamic_pay_license_key';

*/

//////////////////////////////////////////////////
// Options
//////////////////////////////////////////////////

$options = array(
	'pronamic_ideal_key' => 'pronamic_pay_license_key',
);

foreach ( $options as $key_old => $key_new ) {
	$value = get_option( $key_old );

	if ( ! empty( $value ) ) {
		update_option( $key_new, $value );
	}
}
