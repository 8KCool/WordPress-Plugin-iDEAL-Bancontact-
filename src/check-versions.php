<?php

$root_dir = dirname( __DIR__ );

$data = file_get_contents( $root_dir . '/package.json' );
$pkg  = json_decode( $data );

// Check readme.txt
// @see https://github.com/WordPress/WordPress/blob/4.9/wp-includes/functions.php#L4810-L4868
$filename = 'readme.txt';

$readme_txt_lines = file( $root_dir . '/' . $filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

$search_string = sprintf( 'Stable tag: %s', $pkg->version );

if ( ! in_array( $search_string, $readme_txt_lines, true ) ) {
	printf( '❌  ' );
	printf( 'Could not find "%s" in file "%s".', $search_string, $filename );

	exit( 1 );
}

printf( '✅  ' );
printf( 'Found "%s" in file "%s".', $search_string, $filename );

echo PHP_EOL;

// Check plugin
// @see https://codex.wordpress.org/File_Header
// @see https://github.com/WordPress/WordPress/blob/4.9/wp-includes/functions.php#L4810-L4868
$filename = 'pronamic-ideal.php';

$plugin_file = file_get_contents( $root_dir . '/' . $filename );

$file_header = '';

$tokens = token_get_all( $plugin_file );

foreach ( $tokens as $token ) {
	if ( is_array( $token ) ) {
		$type  = $token[0];
		$value = $token[1];
		$line  = $token[2];

		if ( in_array( $type, array( T_COMMENT, T_DOC_COMMENT ), true ) && false !== strpos( $value, 'Plugin Name' ) ) {
			$file_header = $value;

			break;
		}
	}
}

$file_header_lines = explode( "\n", $file_header );
$file_header_lines = array_map( function( $value ) { return ltrim( $value, '*' ); }, $file_header_lines );
$file_header_lines = array_map( 'trim', $file_header_lines );

$search_string = sprintf( 'Version: %s', $pkg->version );

if ( ! in_array( $search_string, $file_header_lines, true ) ) {
	printf( '❌  ' );
	printf( 'Could not find "%s" in file "%s".', $search_string, $filename );

	exit( 1 );
}

printf( '✅  ' );
printf( 'Found "%s" in file "%s".', $search_string, $filename );

echo PHP_EOL;
