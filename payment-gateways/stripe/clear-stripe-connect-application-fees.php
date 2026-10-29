<?php
/**
 * Find and clear Stripe Connect application fees left on existing Stripe subscriptions and draft invoices.
 *
 * title: Find and Clear Leftover Stripe Connect Application Fees on Existing Subscriptions
 * layout: snippet
 * collection: payment-gateways, stripe
 * category: fee
 * link: TBD
 *
 * Adds a Tools > Stripe Application Fees page for administrators. The page lists open subscriptions
 * and draft invoices in your Stripe account that still carry an application fee. Nothing changes
 * until you click the "Clear These Fees" button.
 *
 * Run it while your site is still connected through Stripe Connect if you can. After you disconnect,
 * it uses the Restricted Key saved in your Stripe settings instead. That key needs Write access to
 * Subscriptions and Invoices.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add the Tools > Stripe Application Fees page.
 */
function my_pmpro_stripe_fees_admin_menu() {
	add_management_page( 'Stripe Application Fees', 'Stripe Application Fees', 'manage_options', 'my-pmpro-stripe-fees', 'my_pmpro_stripe_fees_page' );
}
add_action( 'admin_menu', 'my_pmpro_stripe_fees_admin_menu' );

/**
 * Get a Stripe client and the fee value to send, preferring the Stripe Connect credentials.
 *
 * @return array|WP_Error
 */
function my_pmpro_stripe_fees_get_client() {
	if ( ! class_exists( 'PMProGateway_stripe' ) ) {
		return new WP_Error( 'no_pmpro', 'Paid Memberships Pro is not active.' );
	}

	PMProGateway_stripe::loadStripeLibrary();
	$environment = get_option( 'pmpro_gateway_environment' ) === 'live' ? 'live' : 'sandbox';

	// The Connect token acts as the platform, which Stripe allows to change the fee.
	$connect_key = get_option( 'pmpro_' . $environment . '_stripe_connect_secretkey' );
	if ( ! empty( $connect_key ) ) {
		return array(
			'client'      => new \Stripe\StripeClient( $connect_key ),
			'credential'  => 'Stripe Connect',
			'environment' => $environment,
			'fee_value'   => 0,
		);
	}

	$api_key = get_option( 'pmpro_stripe_secretkey' );
	if ( ! empty( $api_key ) ) {
		// Account owners unset the fee rather than set a value.
		return array(
			'client'      => new \Stripe\StripeClient( $api_key ),
			'credential'  => 'your Restricted Key',
			'environment' => $environment,
			'fee_value'   => '',
		);
	}

	return new WP_Error( 'no_credentials', 'No Stripe credentials found in Memberships > Settings > Payment Gateway.' );
}

/**
 * Request options that make Stripe include application_fee_amount on invoices.
 *
 * @return array
 */
function my_pmpro_stripe_fees_invoice_opts() {
	return array( 'stripe_version' => '2025-02-24.acacia' );
}

/**
 * Find open subscriptions and draft invoices that carry an application fee.
 *
 * @param \Stripe\StripeClient $client Stripe client.
 * @return array
 */
function my_pmpro_stripe_fees_find( $client ) {
	$found = array(
		'subscriptions' => array(),
		'invoices'      => array(),
	);

	$subscriptions = $client->subscriptions->all( array( 'status' => 'all', 'limit' => 100 ) );
	foreach ( $subscriptions->autoPagingIterator() as $subscription ) {
		if ( in_array( $subscription->status, array( 'canceled', 'incomplete_expired' ), true ) ) {
			continue;
		}
		if ( ! empty( $subscription->application_fee_percent ) ) {
			$found['subscriptions'][] = $subscription;
		}
	}

	// Invoices only show application_fee_amount on API versions before 2025-03-31.
	$invoices = $client->invoices->all( array( 'status' => 'draft', 'limit' => 100 ), my_pmpro_stripe_fees_invoice_opts() );
	foreach ( $invoices->autoPagingIterator() as $invoice ) {
		if ( ! empty( $invoice->application_fee_amount ) ) {
			$found['invoices'][] = $invoice;
		}
	}

	return $found;
}

/**
 * Show the report and handle the clear request.
 */
function my_pmpro_stripe_fees_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	echo '<div class="wrap"><h1>Stripe Application Fees</h1>';

	$setup = my_pmpro_stripe_fees_get_client();
	if ( is_wp_error( $setup ) ) {
		echo '<div class="notice notice-error"><p>' . esc_html( $setup->get_error_message() ) . '</p></div></div>';
		return;
	}

	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 );
	}

	$client = $setup['client'];
	$apply  = isset( $_POST['my_pmpro_stripe_fees_clear'] ) && check_admin_referer( 'my_pmpro_stripe_fees_clear' );

	echo '<p>' . esc_html( sprintf( 'Checking your Stripe account (%s mode) using %s.', $setup['environment'], $setup['credential'] ) ) . '</p>';

	try {
		$found = my_pmpro_stripe_fees_find( $client );
	} catch ( Exception $e ) {
		echo '<div class="notice notice-error"><p>' . esc_html( 'Stripe error: ' . $e->getMessage() ) . '</p></div></div>';
		return;
	}

	if ( empty( $found['subscriptions'] ) && empty( $found['invoices'] ) ) {
		echo '<div class="notice notice-success"><p>No open subscriptions or draft invoices carry an application fee.</p></div></div>';
		return;
	}

	echo '<table class="widefat striped"><thead><tr><th>Stripe Object</th><th>Customer</th><th>Application Fee</th><th>Result</th></tr></thead><tbody>';

	foreach ( $found['subscriptions'] as $subscription ) {
		$result = '';
		if ( $apply ) {
			try {
				$updated = $client->subscriptions->update( $subscription->id, array( 'application_fee_percent' => $setup['fee_value'] ) );
				$result  = empty( $updated->application_fee_percent ) ? 'Cleared' : 'Still ' . $updated->application_fee_percent . '%';
			} catch ( Exception $e ) {
				$result = 'Error: ' . $e->getMessage();
			}
		}
		echo '<tr><td>' . esc_html( $subscription->id ) . ' (' . esc_html( $subscription->status ) . ')</td><td>' . esc_html( $subscription->customer ) . '</td><td>' . esc_html( $subscription->application_fee_percent ) . '%</td><td>' . esc_html( $result ) . '</td></tr>';
	}

	foreach ( $found['invoices'] as $invoice ) {
		$result = '';
		if ( $apply ) {
			try {
				$updated = $client->invoices->update( $invoice->id, array( 'application_fee_amount' => $setup['fee_value'] ), my_pmpro_stripe_fees_invoice_opts() );
				$result  = empty( $updated->application_fee_amount ) ? 'Cleared' : 'Still ' . $updated->application_fee_amount;
			} catch ( Exception $e ) {
				$result = 'Error: ' . $e->getMessage();
			}
		}
		echo '<tr><td>' . esc_html( $invoice->id ) . ' (draft invoice)</td><td>' . esc_html( $invoice->customer ) . '</td><td>' . esc_html( $invoice->application_fee_amount . ' ' . strtoupper( $invoice->currency ) ) . ' (smallest unit)</td><td>' . esc_html( $result ) . '</td></tr>';
	}

	echo '</tbody></table>';

	if ( $apply ) {
		echo '<p>Done. Reload this page to check again.</p></div>';
		return;
	}

	echo '<form method="post">';
	wp_nonce_field( 'my_pmpro_stripe_fees_clear' );
	echo '<p><input type="submit" class="button button-primary" name="my_pmpro_stripe_fees_clear" value="Clear These Fees" onclick="return confirm(\'Set the application fee to 0 on everything listed above?\');" /></p>';
	echo '</form></div>';
}
