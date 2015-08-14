<h3><?php esc_html_e( 'Payment Gateways', 'pronamic_ideal' ); ?></h3>

<?php

global $pronamic_pay_providers;
global $pronamic_pay_gateways;

bind_providers_and_gateways();

$gateways = $pronamic_pay_gateways;

include 'gateways-wp-admin.php';

$output = array(
	'readme-md'  => 'gateways-readme-md.php',
	'readme-txt' => 'gateways-readme-txt.php',
);

foreach ( $output as $name => $file ) {
	if ( filter_has_var( INPUT_GET, $name ) ) : ?>

		<h4><?php esc_html_e( 'Markdown', 'pronamic_ideal' ); ?></h4>

		<?php

		ob_start();

		include $file;

		$markdown = ob_get_clean();

		?>

		<textarea cols="60" rows="25"><?php echo esc_textarea( $markdown ); ?></textarea>

	<?php endif;
}
