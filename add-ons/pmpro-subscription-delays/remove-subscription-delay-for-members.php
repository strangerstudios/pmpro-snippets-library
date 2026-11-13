<?php
/**
 * Remove subscription delay for logged-in current or past members. 
 * If that user tries to check out for the same level, the Subscription Delay is removed. 
 * The user is instead charged for their first subscription payment at checkout.
 *
 * Written for the Subscription Delays Add On, which is retired and merged into
 * PMPro 3.9. Do not use on PMPro 3.9+: core still delays the first recurring
 * payment, so returning members would be charged at checkout and again when the delay ends.
 *
 * title: Subscription Delay that can only be used once.
 * layout: snippet
 * collection: add-ons, pmpro-subscription-delays
 * category: custom-fields
 * link: https://www.paidmembershipspro.com/subscription-delay-one-time-credit-card-required-trial/
 * Status: Retired — does not work with PMPro 3.9+; use the One-Time Free Trial That Requires a Credit Card recipe instead.
 */
function my_pmpro_one_time_sub_delay( $checkout_level ) {

	// Logged-out users should always get the trial.
	if ( ! is_user_logged_in() ) {
		return $checkout_level;
	}

	$order     = new MemberOrder();
	$lastorder = $order->getLastMemberOrder( null, array( 'success', 'cancelled' ) );
	$has_delay = get_option( 'pmpro_subscription_delay_' . $checkout_level->id, '' );

	// If user currently has a membership level or previously had a membership level, remove subscription delay.
	if ( ( pmpro_hasMembershipLevel() || ! empty( $lastorder ) ) && ! empty( $has_delay ) ) {

		// Remove subscription delay filters and actions (standard).
		remove_filter( 'pmpro_profile_start_date', 'pmprosd_pmpro_profile_start_date', 10, 2 );
		remove_action( 'pmpro_after_checkout', 'pmprosd_pmpro_after_checkout' );
		remove_filter( 'pmpro_next_payment', 'pmprosd_pmpro_next_payment', 10, 3 );
		remove_filter( 'pmpro_level_cost_text', 'pmprosd_level_cost_text', 10, 2 );
		remove_action( 'pmpro_save_discount_code_level', 'pmprosd_pmpro_save_discount_code_level', 10, 2 );

		// Remove the updated filter added in PMPro Subscription Delays 3.4+.
		remove_filter( 'pmpro_checkout_level', 'pmprosd_pmpro_checkout_level', 10, 2 );

		// Set the initial amount to match the billing amount.
		if ( $checkout_level->billing_amount > 0 ) {
			$checkout_level->initial_payment = $checkout_level->billing_amount;
		}
	}

	return $checkout_level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_one_time_sub_delay' );
