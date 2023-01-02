<?php
/**
 * Readme gateways.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

$data = file_get_contents( __DIR__ . '/../providers.json' );

// Check if file could be read.
if ( false === $data ) {
	return;
}

$data = json_decode( $data );

$providers = [];

foreach ( $data as $provider ) {
	$providers[ $provider->slug ] = $provider;
}

$data = file_get_contents( __DIR__ . '/../gateways.json' );

// Check if file could be read.
if ( false === $data ) {
	return;
}

$gateways = json_decode( $data );

foreach ( $gateways as $gateway ) {
	if ( ! isset( $providers[ $gateway->provider ] ) ) {
		continue;
	}

	$provider = $providers[ $gateway->provider ];

	if ( ! isset( $provider->gateways ) ) {
		$provider->gateways = [];
	}

	$provider->gateways[ $gateway->slug ] = $gateway;
}

?>
| Provider | Name |
| -------- | ---- |
<?php foreach ( $gateways as $gateway ) : ?>
| <?php

if ( isset( $gateway->provider, $providers[ $gateway->provider ] ) ) {
	$provider = $providers[ $gateway->provider ];

	if ( isset( $provider->url, $provider->name ) ) {
		printf( '[%s](%s)', $provider->name, $provider->url );
	} else {
		echo $provider->name;
	}
}

echo ' | ';

echo $gateway->name;

?> |
<?php endforeach; ?>
