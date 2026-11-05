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
 * Adds a Tools > Stripe Application Fees page for administrators. Click "Find Fees" to list open
 * subscriptions and draft invoices in your Stripe account that still carry an application fee.
 * Nothing changes until you click the "Clear These Fees" button.
 *
 * Large accounts are processed in small batches. Keep the page open until it says it is done.
 * If a batch fails, click Continue to retry it.
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
		// This key is not tied to the gateway environment setting, so read the mode from the key itself.
		$key_environment = ( 0 === strpos( $api_key, 'sk_live_' ) || 0 === strpos( $api_key, 'rk_live_' ) ) ? 'live' : 'sandbox';

		// Account owners unset the fee rather than set a value.
		return array(
			'client'      => new \Stripe\StripeClient( $api_key ),
			'credential'  => 'your Restricted Key',
			'environment' => $key_environment,
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
 * Key for the current admin's saved run.
 *
 * @return string
 */
function my_pmpro_stripe_fees_state_key() {
	return 'my_pmpro_stripe_fees_run_' . get_current_user_id();
}

/**
 * A fresh run.
 *
 * @param string $mode        'scan' or 'clear'.
 * @param string $environment 'live' or 'sandbox'.
 * @return array
 */
function my_pmpro_stripe_fees_new_state( $mode, $environment ) {
	return array(
		'mode'        => $mode,
		'environment' => $environment,
		'phase'       => 'subscriptions', // subscriptions > invoices > done.
		'cursor'      => '',
		'checked'     => 0,
		'found'       => 0,
		'cleared'     => 0,
		'errors'      => 0,
		'rows'        => array(),
	);
}

/**
 * Add a found object to the run totals. Only the first 500 rows are kept for display.
 *
 * @param array $state Run state.
 * @param array $row   Row to show.
 * @return array
 */
function my_pmpro_stripe_fees_record( $state, $row ) {
	$state['found']++;
	if ( 'Cleared' === $row['result'] ) {
		$state['cleared']++;
	} elseif ( 0 === strpos( $row['result'], 'Error' ) ) {
		$state['errors']++;
	}
	if ( count( $state['rows'] ) < 500 ) {
		$state['rows'][] = $row;
	}
	return $state;
}

/**
 * Process one page of Stripe results and move the cursor forward.
 * Throws if the Stripe list call fails, leaving the cursor where it was so the step can be retried.
 *
 * @param array $setup From my_pmpro_stripe_fees_get_client().
 * @param array $state Run state.
 * @return array Updated run state.
 */
function my_pmpro_stripe_fees_run_step( $setup, $state ) {
	$client = $setup['client'];
	$apply  = 'clear' === $state['mode'];

	// Each fee update is its own API call, so clearing uses smaller pages to stay well under request timeouts.
	$params = array( 'limit' => $apply ? 25 : 100 );
	if ( ! empty( $state['cursor'] ) ) {
		$params['starting_after'] = $state['cursor'];
	}

	if ( 'subscriptions' === $state['phase'] ) {
		// With no status filter, Stripe returns every subscription that is not canceled.
		$page = $client->subscriptions->all( $params );
		foreach ( $page->data as $subscription ) {
			$state['checked']++;
			if ( 'incomplete_expired' === $subscription->status || empty( $subscription->application_fee_percent ) ) {
				continue;
			}

			$row = array(
				'object'   => $subscription->id . ' (' . $subscription->status . ')',
				'customer' => (string) $subscription->customer,
				'fee'      => $subscription->application_fee_percent . '%',
				'result'   => '',
			);
			if ( $apply ) {
				try {
					$updated       = $client->subscriptions->update( $subscription->id, array( 'application_fee_percent' => $setup['fee_value'] ) );
					$row['result'] = empty( $updated->application_fee_percent ) ? 'Cleared' : 'Still ' . $updated->application_fee_percent . '%';
				} catch ( Exception $e ) {
					$row['result'] = 'Error: ' . $e->getMessage();
				}
			}
			$state = my_pmpro_stripe_fees_record( $state, $row );
		}
	} else {
		// Only drafts can change. Finalized invoices keep whatever fee they were finalized with.
		$params['status'] = 'draft';
		$page             = $client->invoices->all( $params, my_pmpro_stripe_fees_invoice_opts() );
		foreach ( $page->data as $invoice ) {
			$state['checked']++;
			if ( empty( $invoice->application_fee_amount ) ) {
				continue;
			}

			$row = array(
				'object'   => $invoice->id . ' (draft invoice)',
				'customer' => (string) $invoice->customer,
				'fee'      => $invoice->application_fee_amount . ' ' . strtoupper( $invoice->currency ) . ' (smallest unit)',
				'result'   => '',
			);
			if ( $apply ) {
				try {
					$updated       = $client->invoices->update( $invoice->id, array( 'application_fee_amount' => $setup['fee_value'] ), my_pmpro_stripe_fees_invoice_opts() );
					$row['result'] = empty( $updated->application_fee_amount ) ? 'Cleared' : 'Still ' . $updated->application_fee_amount;
				} catch ( Exception $e ) {
					$row['result'] = 'Error: ' . $e->getMessage();
				}
			}
			$state = my_pmpro_stripe_fees_record( $state, $row );
		}
	}

	$items = $page->data;
	if ( $page->has_more && ! empty( $items ) ) {
		$state['cursor'] = end( $items )->id;
	} else {
		$state['cursor'] = '';
		$state['phase']  = 'subscriptions' === $state['phase'] ? 'invoices' : 'done';
	}

	return $state;
}

/**
 * Print a one-button form that posts back to this page.
 *
 * @param string $action  Value sent as my_pmpro_stripe_fees_action.
 * @param string $label   Button label.
 * @param string $confirm Optional confirm() message.
 * @param bool   $primary Primary button styling.
 * @param string $id      Optional form ID.
 */
function my_pmpro_stripe_fees_button( $action, $label, $confirm = '', $primary = true, $id = '' ) {
	echo '<form method="post" style="display:inline-block;margin-right:8px;"' . ( $id ? ' id="' . esc_attr( $id ) . '"' : '' ) . '>';
	wp_nonce_field( 'my_pmpro_stripe_fees' );
	echo '<input type="hidden" name="my_pmpro_stripe_fees_action" value="' . esc_attr( $action ) . '" />';
	echo '<input type="submit" class="button' . ( $primary ? ' button-primary' : '' ) . '" value="' . esc_attr( $label ) . '"';
	if ( $confirm ) {
		echo ' onclick="return confirm(' . esc_attr( wp_json_encode( $confirm ) ) . ');"';
	}
	echo ' /></form>';
}

/**
 * Show the page, run one batch per request, and keep going until done.
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

	echo '<p>' . esc_html( sprintf( 'Checking your Stripe account (%s mode) using %s.', $setup['environment'], $setup['credential'] ) ) . '</p>';

	$state_key = my_pmpro_stripe_fees_state_key();
	$state     = get_transient( $state_key );

	// A saved run from the other mode (for example, before switching keys) no longer applies.
	if ( is_array( $state ) && $state['environment'] !== $setup['environment'] ) {
		$state = false;
	}

	$step_failed = false;
	$action      = isset( $_POST['my_pmpro_stripe_fees_action'] ) ? sanitize_key( wp_unslash( $_POST['my_pmpro_stripe_fees_action'] ) ) : '';
	if ( $action ) {
		check_admin_referer( 'my_pmpro_stripe_fees' );

		if ( 'scan' === $action ) {
			$state = my_pmpro_stripe_fees_new_state( 'scan', $setup['environment'] );
		} elseif ( 'clear' === $action && is_array( $state ) && 'scan' === $state['mode'] && 'done' === $state['phase'] && $state['found'] > 0 ) {
			$state = my_pmpro_stripe_fees_new_state( 'clear', $setup['environment'] );
		}

		if ( is_array( $state ) && 'done' !== $state['phase'] ) {
			if ( function_exists( 'set_time_limit' ) ) {
				set_time_limit( 120 );
			}
			try {
				$state = my_pmpro_stripe_fees_run_step( $setup, $state );
			} catch ( Exception $e ) {
				$step_failed = true;
				echo '<div class="notice notice-error"><p>' . esc_html( 'Stripe error: ' . $e->getMessage() . ' Click Continue to retry this batch.' ) . '</p></div>';
			}
			set_transient( $state_key, $state, DAY_IN_SECONDS );
		}
	}

	if ( ! is_array( $state ) ) {
		echo '<p>Find open subscriptions and draft invoices that still carry an application fee. Nothing changes until you confirm.</p>';
		my_pmpro_stripe_fees_button( 'scan', 'Find Fees' );
		echo '</div>';
		return;
	}

	$verb = 'clear' === $state['mode'] ? 'Clearing' : 'Finding';
	if ( 'done' !== $state['phase'] ) {
		echo '<p><strong>' . esc_html( sprintf( '%s fees in %s... checked %d so far, %d with a fee. Keep this page open.', $verb, 'subscriptions' === $state['phase'] ? 'subscriptions' : 'draft invoices', $state['checked'], $state['found'] ) ) . '</strong></p>';
	} elseif ( 'scan' === $state['mode'] ) {
		echo '<p>' . esc_html( sprintf( 'Checked %d open subscriptions and draft invoices. Found %d with an application fee.', $state['checked'], $state['found'] ) ) . '</p>';
	} else {
		echo '<p>' . esc_html( sprintf( 'Done. %d cleared, %d errors, out of %d with a fee.', $state['cleared'], $state['errors'], $state['found'] ) ) . '</p>';
	}

	if ( ! empty( $state['rows'] ) ) {
		echo '<table class="widefat striped" style="margin:1em 0;"><thead><tr><th>Stripe Object</th><th>Customer</th><th>Application Fee</th><th>Result</th></tr></thead><tbody>';
		foreach ( $state['rows'] as $row ) {
			echo '<tr><td>' . esc_html( $row['object'] ) . '</td><td>' . esc_html( $row['customer'] ) . '</td><td>' . esc_html( $row['fee'] ) . '</td><td>' . esc_html( $row['result'] ) . '</td></tr>';
		}
		echo '</tbody></table>';
		if ( $state['found'] > count( $state['rows'] ) ) {
			echo '<p>' . esc_html( sprintf( 'Showing the first %d of %d.', count( $state['rows'] ), $state['found'] ) ) . '</p>';
		}
	}

	if ( 'done' !== $state['phase'] ) {
		if ( ! $action ) {
			echo '<p>' . esc_html( sprintf( 'An earlier run stopped before it finished. Click Continue to keep %s where it left off.', 'clear' === $state['mode'] ? 'clearing fees' : 'searching' ) ) . '</p>';
		}
		my_pmpro_stripe_fees_button( 'continue', 'Continue', '', true, 'my-pmpro-stripe-fees-continue' );
		// Only auto-continue right after a successful batch in this request.
		// After an error, or when the page is just opened, wait for a click.
		if ( $action && ! $step_failed ) {
			echo '<script>document.getElementById("my-pmpro-stripe-fees-continue").submit();</script>';
		}
	} else {
		if ( 'scan' === $state['mode'] && $state['found'] > 0 ) {
			my_pmpro_stripe_fees_button( 'clear', 'Clear These Fees', sprintf( 'Remove the application fee from %d Stripe objects?', $state['found'] ) );
		}
		my_pmpro_stripe_fees_button( 'scan', 'Find Fees Again', '', 'scan' !== $state['mode'] || 0 === $state['found'] );
		echo '<p class="description">Only draft invoices can change. Invoices that are already finalized keep their fee.</p>';
	}

	echo '</div>';
}
