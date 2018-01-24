<?php

namespace Pronamic\WordPress\Pay\Forms;

use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Title: Forms module
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 3.7.0
 * @since 3.7.0
 */
class FormsModule {
	/**
	 * Constructs and initalize a forms module object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Form Post Type
		$this->form_post_type = new FormPostType();

		// Processor
		$this->processor = new FormProcessor( $plugin );

		// Scripts
		$this->scripts = new FormScripts( $plugin );

		// Shortcode
		$this->shortcode = new FormShortcode( $this );

		// Actions
		add_filter( 'the_content', array( $this, 'maybe_add_form_to_content' ) );

		add_filter( 'pronamic_payment_source_text_payment_form', array( $this, 'source_text' ), 10, 2 );
	}

	/**
	 * Maybe add form to content.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/the_content/
	 * @param string $content
	 * @return string
	 */
	public function maybe_add_form_to_content( $content ) {
		if ( is_singular( 'pronamic_pay_form' ) && 'pronamic_pay_form' === get_post_type() ) {
			$content .= $this->get_form_output( get_the_ID() );
		}

		return $content;
	}

	/**
	 * Get form output.
	 *
	 * @param string $id
	 * @return string
	 */
	public function get_form_output( $id ) {
		$file = plugin_dir_path( Plugin::$file ) . 'templates/form.php';

		ob_start();

		include $file;

		$output = ob_get_clean();

		return $output;
	}

	//////////////////////////////////////////////////

	/**
	 * Source text
	 */
	public function source_text( $text, Payment $payment ) {
		$text = __( 'Payment Form', 'pronamic_ideal' ) . '<br />';

		$text .= sprintf(
			'<a href="%s">%s</a>',
			get_edit_post_link( $payment->source_id ),
			$payment->source_id
		);

		return $text;
	}
}
