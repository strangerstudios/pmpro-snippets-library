<?php
/**
 * Send billing failure emails for stale Paystack subscriptions.
 * 
 * Runs hourly via PMPro's built-in Action Scheduler event (pmpro_schedule_hourly),
 * processing subscriptions in small batches to stay well within Paystack API rate
 * limits. Checks the Paystack API as the source of truth — only acts on 'attention'
 * or 'cancelled' statuses. Sends billing failure emails once per subscription and
 * flags it to prevent repeats.
 *
 * title: Send Billing Failure Emails for Stale Paystack Subscriptions
 * layout: snippet
 * collection: gateway, paystack
 * category: billing, email
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
if ( ! defined( 'MY_PMPRO_PAYSTACK_BATCH_SIZE' ) ) {
	define( 'MY_PMPRO_PAYSTACK_BATCH_SIZE', 25 );
}
if ( ! defined( 'MY_PMPRO_PAYSTACK_OFFSET_KEY' ) ) {
	define( 'MY_PMPRO_PAYSTACK_OFFSET_KEY', 'my_pmpro_paystack_stale_check_offset' );
}

add_action( 'pmpro_schedule_hourly', 'my_pmpro_paystack_check_stale_subscriptions' );
add_action( 'pmpro_updated_order', 'my_pmpro_paystack_clear_stale_flag' );

/**
 * Hourly batch check for stale Paystack subscriptions.
 *
 * Fetches a batch of active Paystack subscriptions with an overdue payment date,
 * confirms failure via the Paystack API, and sends billing failure emails once
 * per subscription. Advances a stored offset so each run processes the next batch.
 */
function my_pmpro_paystack_check_stale_subscriptions() {
	if ( ! function_exists( 'pmpro_changeMembershipLevel' ) || ! class_exists( 'PMPro_Subscription' ) ) {
		return;
	}

	$secret_key = get_option( 'pmpro_paystack_lsk' );
	if ( empty( $secret_key ) ) {
		return;
	}

	$offset = (int) get_option( MY_PMPRO_PAYSTACK_OFFSET_KEY, 0 );

	$subscriptions = PMPro_Subscription::get_subscriptions(
		array(
			'gateway' => 'paystack',
			'status'  => 'active',
			'limit'   => MY_PMPRO_PAYSTACK_BATCH_SIZE,
			'offset'  => $offset,
		)
	);

	// Batch is empty — we've cycled through all subscriptions. Reset the offset.
	if ( empty( $subscriptions ) ) {
		update_option( MY_PMPRO_PAYSTACK_OFFSET_KEY, 0 );
		return;
	}

	$now = time();

	foreach ( $subscriptions as $subscription ) {
		$next_payment_date = $subscription->get_next_payment_date();

		// Skip subscriptions whose payment isn't overdue yet.
		if ( empty( $next_payment_date ) || strtotime( $next_payment_date ) > $now ) {
			continue;
		}

		// Skip if we've already sent the stale notification for this subscription.
		$flag_key = '_pmpro_paystack_stale_notified_' . $subscription->get_id();
		if ( get_user_meta( $subscription->get_user_id(), $flag_key, true ) ) {
			continue;
		}

		$subscription_code = $subscription->get_subscription_transaction_id();
		if ( empty( $subscription_code ) ) {
			continue;
		}

		// Confirm status via the Paystack API — it is the source of truth.
		$response = wp_remote_get(
			'https://api.paystack.co/subscription/' . rawurlencode( $subscription_code ),
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $secret_key,
					'Content-Type'  => 'application/json',
				),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			continue;
		}

		$body            = json_decode( wp_remote_retrieve_body( $response ), true );
		$paystack_status = isset( $body['data']['status'] ) ? $body['data']['status'] : '';

		if ( ! in_array( $paystack_status, array( 'attention', 'cancelled' ), true ) ) {
			continue;
		}

		$user = get_userdata( $subscription->get_user_id() );
		if ( empty( $user ) ) {
			continue;
		}

		$orders = $subscription->get_orders( array( 'limit' => 1 ) );
		$order  = ! empty( $orders ) ? reset( $orders ) : null;

		if ( ! empty( $order ) ) {
			$member_email = new PMProEmail();
			$member_email->sendBillingFailureEmail( $user, $order );

			$admin_email = new PMProEmail();
			$admin_email->sendBillingFailureAdminEmail( get_bloginfo( 'admin_email' ), $order );

			// Flag this subscription so the notification is never sent again.
			update_user_meta( $subscription->get_user_id(), $flag_key, current_time( 'mysql' ) );
		}
	}

	// Fewer results than the batch size means we've reached the end — reset the offset.
	if ( count( $subscriptions ) < MY_PMPRO_PAYSTACK_BATCH_SIZE ) {
		update_option( MY_PMPRO_PAYSTACK_OFFSET_KEY, 0 );
	} else {
		update_option( MY_PMPRO_PAYSTACK_OFFSET_KEY, $offset + MY_PMPRO_PAYSTACK_BATCH_SIZE );
	}
}

/**
 * Clear the stale-notification flag when a successful subscription payment comes in.
 *
 * Fires on pmpro_updated_order so the next billing-failure cycle
 * can send a fresh notification if the subscription goes stale again later.
 *
 * @param MemberOrder $order The updated order.
 */
function my_pmpro_paystack_clear_stale_flag( $order ) {
	if ( empty( $order->user_id ) || empty( $order->subscription_transaction_id ) ) {
		return;
	}

	if ( 'success' !== $order->status ) {
		return;
	}

	$subscription = PMPro_Subscription::get_subscription_from_subscription_transaction_id(
		$order->subscription_transaction_id,
		$order->gateway,
		$order->gateway_environment
	);

	if ( empty( $subscription ) ) {
		return;
	}

	$flag_key = '_pmpro_paystack_stale_notified_' . $subscription->get_id();
	delete_user_meta( $order->user_id, $flag_key );
}