<?php

/**
 * Title: WordPress iDEAL plugin
 * Description:
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_WordPress_IDeal_Plugin {
	/**
	 * The maximum number of payments that can be done without an license
	 *
	 * @var int
	 */
	const PAYMENTS_MAX_LICENSE_FREE = 20;

	//////////////////////////////////////////////////

	/**
	 * The root file of this WordPress plugin
	 *
	 * @var string
	 */
	public static $file;

	/**
	 * The plugin dirname
	 *
	 * @var string
	 */
	public static $dirname;

	//////////////////////////////////////////////////

	/**
	 * Bootstrap
	 *
	 * @param string $file
	 */
	public static function bootstrap( $file ) {
		self::$file    = $file;
		self::$dirname = dirname( $file );

		// Bootstrap the add-ons
		if ( self::can_be_used() ) {
			Pronamic_WooCommerce_IDeal_AddOn::bootstrap();
			Pronamic_GravityForms_IDeal_AddOn::bootstrap();
			Pronamic_Shopp_IDeal_AddOn::bootstrap();
			Pronamic_Jigoshop_IDeal_AddOn::bootstrap();
			Pronamic_WPeCommerce_IDeal_AddOn::bootstrap();
			Pronamic_ClassiPress_IDeal_AddOn::bootstrap();
			Pronamic_EShop_IDeal_AddOn::bootstrap();
			Pronamic_EventEspresso_IDeal_AddOn::bootstrap();
			Pronamic_AppThemes_IDeal_AddOn::bootstrap();
			Pronamic_S2Member_IDeal_AddOn::bootstrap();
			Pronamic_Membership_IDeal_AddOn::bootstrap();
		}

		// Admin
		if ( is_admin() ) {
			Pronamic_WordPress_IDeal_Admin::bootstrap();
		}

		add_action( 'plugins_loaded', array( __CLASS__, 'setup' ) );

		// Initialize requirements
		require_once self::$dirname . '/includes/version.php';
		require_once self::$dirname . '/includes/functions.php';
		require_once self::$dirname . '/includes/page-functions.php';
		require_once self::$dirname . '/includes/gravityforms.php';
		require_once self::$dirname . '/includes/providers.php';
		require_once self::$dirname . '/includes/gateways.php';
		require_once self::$dirname . '/includes/post.php';
		require_once self::$dirname . '/includes/xmlseclibs/xmlseclibs-ing.php';
		require_once self::$dirname . '/includes/wp-e-commerce.php';

		// On template redirect handle an possible return from iDEAL
		add_action( 'template_redirect', array( __CLASS__, 'handle_returns' ) );

		// The 'pronamic_ideal_check_transaction_status' hook is scheduled the status requests
		add_action( 'pronamic_ideal_check_transaction_status', array( __CLASS__, 'checkStatus' ) );

		// Show license message if the license is not valid
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
	}

	//////////////////////////////////////////////////

	/**
	 * Check status of the specified payment
	 *
	 * @param string $paymentId
	 */
	public static function checkStatus( $payment_id = null ) {
		$payment = new Pronamic_WP_Pay_Payment( $payment_id );

		if ( $payment !== null ) {
			// http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Advanced_PHP_EN_V2.2.pdf (page 19)
			// - No status request after a final status has been received for a transaction;
			$status = $payment->status;

			if ( empty( $status ) || $status === Pronamic_Gateways_IDealAdvancedV3_Status::OPEN ) {
				self::update_payment( $payment );
			}
		} else {
			// Payment with the specified ID could not be found, can't check the status
		}
	}

	public static function update_payment( $payment = null, $can_redirect = true ) {
		if ( $payment ) {
			$gateway = Pronamic_WordPress_IDeal_IDeal::get_gateway( $payment->config_id );

			if ( $gateway ) {
				$gateway->update_status( $payment );

				update_post_meta( $payment->get_id(), '_pronamic_payment_status', $payment->status );
				update_post_meta( $payment->get_id(), '_pronamic_payment_consumer_name', $payment->consumer_name );
				update_post_meta( $payment->get_id(), '_pronamic_payment_consumer_account_number', $payment->consumer_account_number );
				update_post_meta( $payment->get_id(), '_pronamic_payment_consumer_iban', $payment->consumer_iban );
				update_post_meta( $payment->get_id(), '_pronamic_payment_consumer_bic', $payment->consumer_bic );
				update_post_meta( $payment->get_id(), '_pronamic_payment_consumer_city', $payment->consumer_city );

				do_action( 'pronamic_payment_status_update_' . $payment->source, $payment, $can_redirect );
				do_action( 'pronamic_payment_status_update', $payment, $can_redirect );
			}
		}
	}
	
	//////////////////////////////////////////////////

	/**
	 * Handle returns
	 */
	public static function handle_returns() {
		if ( filter_has_var( INPUT_GET, 'payment' ) ) {
			$payment_id = filter_input( INPUT_GET, 'payment', FILTER_SANITIZE_NUMBER_INT );
			
			$payment = get_pronamic_payment( $payment_id );

			self::update_payment( $payment );
		}

		Pronamic_Gateways_IDealBasic_Listener::listen();
		Pronamic_Gateways_OmniKassa_Listener::listen();
		Pronamic_Gateways_Icepay_Listener::listen();
	}

	//////////////////////////////////////////////////

	/**
	 * Get the key
	 *
	 * @return string
	 */
	public static function get_key() {
		return get_option( 'pronamic_ideal_key' );
	}

	/**
	 * Get the license info for the current installation on the blogin
	 *
	 * @return stdClass an onbject with license information or null
	 */
	public static function get_license_info() {
		 return null;
	}

	/**
	 * Check if there is an valid license key
	 *
	 * @return boolean
	 */
	public static function has_valid_key() {
		$result = strlen( self::get_key() ) == 32;

		$license_info = self::get_license_info();

		if ( $license_info != null && isset( $license_info->isValid ) ) {
			$result = $license_info->isValid;
		}

		return $result;
	}

	/**
	 * Checks if the plugin is installed
	 */
	public static function is_installed() {
		return get_option( 'pronamic_pay_version', false ) !== false;
	}

	/**
	 * Check if the plugin can be used
	 *
	 * @return boolean true if plugin can be used, false otherwise
	 */
	public static function can_be_used() {
		return self::is_installed() && self::has_valid_key();
	}

	//////////////////////////////////////////////////

	/**
	 * Maybe show an license message
	 */
	public static function admin_notices() {
		if ( ! self::can_be_used() ): ?>

			<div class="error">
				<p>
					<?php

					printf(
						__( '<strong>Pronamic iDEAL limited:</strong> You exceeded the maximum free payments of %d, you should enter an valid license key on the <a href="%s">iDEAL settings page</a>.', 'pronamic_ideal' ),
						self::PAYMENTS_MAX_LICENSE_FREE,
						add_query_arg( 'page', 'pronamic_ideal_settings', get_admin_url( null, 'admin.php' ) )
					);

					?>
				</p>
			</div>

		<?php elseif ( ! self::has_valid_key() ) : ?>

			<div class="updated">
				<p>
					<?php

					printf(
						__( 'You can <a href="%s">enter your Pronamic iDEAL API key</a> to use extra extensions, get support and more than %d payments.', 'pronamic_ideal' ),
						add_query_arg( 'page', 'pronamic_ideal_settings', get_admin_url( null, 'admin.php' ) ),
						self::PAYMENTS_MAX_LICENSE_FREE
					);

					?>
				</p>
			</div>

		<?php endif;
	}

	//////////////////////////////////////////////////

	/**
	 * Configure the specified roles
	 *
	 * @param array $roles
	 */
	public static function set_roles( $roles ) {
		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		foreach ( $roles as $role => $data ) {
			if ( isset( $data['display_name'], $data['capabilities'] ) ) {
				$display_name = $data['display_name'];
				$capabilities = $data['capabilities'];

				if ( $wp_roles->is_role( $role ) ) {
					foreach ( $capabilities as $cap => $grant ) {
						$wp_roles->add_cap( $role, $cap, $grant );
					}
				} else {
					$wp_roles->add_role( $role, $display_name, $capabilities );
				}
			}
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Setup, creates or updates database tables. Will only run when version changes
	 */
	public static function setup() {
		// Load plugin text domain
		$rel_path = dirname( plugin_basename( self::$file ) ) . '/languages/';

		load_plugin_textdomain( 'pronamic_ideal', false, $rel_path );

		global $pronamic_pay_version;

		if ( get_option( 'pronamic_pay_version' ) != $pronamic_pay_version ) {
			// Add some new capabilities
			$capabilities = array(
				'read'                           => true,
				'pronamic_ideal'                 => true,
				'pronamic_ideal_configurations'  => true,
				'pronamic_ideal_payments'        => true,
				'pronamic_ideal_settings'        => true,
				'pronamic_ideal_pages_generator' => true,
				'pronamic_ideal_status'          => true,
				'pronamic_ideal_providers'       => true,
				'pronamic_ideal_variants'        => true,
				'pronamic_ideal_documentation'   => true,
				'pronamic_ideal_branding'        => true
			);

			$roles = array(
				'pronamic_ideal_administrator' => array(
					'display_name' => __( 'iDEAL Administrator', 'pronamic_ideal' ),
					'capabilities' => $capabilities
				) ,
				'administrator' => array(
					'display_name' => __( 'Administrator', 'pronamic_ideal' ),
					'capabilities' => $capabilities
				)
			);

			self::set_roles( $roles );

			// Update version
			update_option( 'pronamic_pay_version', $pronamic_pay_version );
		}
	}
}
