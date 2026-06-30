<?php
/**
 * Downgrade a member to a fallback level when a recurring payment fails,
 * then restore their original level when a later payment succeeds.
 *
 * title: Downgrade member to fallback level on failed payment and restore on success
 * layout: snippet
 * collection: membership-levels
 * category: subscription-handling
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
// Level to assign when payment fails. Use 0 to cancel membership.
if ( ! defined( 'MY_PMPRO_FALLBACK_LEVEL_ID' ) ) {
	define( 'MY_PMPRO_FALLBACK_LEVEL_ID', 1 );
}

// Stores the member's original level ID.
if ( ! defined( 'MY_PMPRO_ORIGINAL_LEVEL_META_KEY' ) ) {
	define( 'MY_PMPRO_ORIGINAL_LEVEL_META_KEY', 'my_pmpro_pre_failure_level_id' );
}

function my_pmpro_downgrade_on_payment_failure( $order ) {

	$user_id     = (int) $order->user_id;
	$paid_level  = (int) $order->membership_id;
	$fallback_id = (int) MY_PMPRO_FALLBACK_LEVEL_ID;

	if ( empty( $user_id ) || empty( $paid_level ) ) {
		return;
	}

	$current_level = pmpro_getMembershipLevelForUser( $user_id );

	// Avoid repeatedly downgrading.
	if ( ! empty( $current_level ) && (int) $current_level->id === $fallback_id ) {
		return;
	}
	if ( 0 === $fallback_id && empty( $current_level ) ) {
		return;
	}

	update_user_meta(
		$user_id,
		MY_PMPRO_ORIGINAL_LEVEL_META_KEY,
		$paid_level
	);

	// Keep subscription retries active.
	add_filter( 'pmpro_cancel_previous_subscriptions', '__return_false' );

	pmpro_changeMembershipLevel( $fallback_id, $user_id );

	remove_filter( 'pmpro_cancel_previous_subscriptions', '__return_false' );
}
add_action( 'pmpro_subscription_payment_failed', 'my_pmpro_downgrade_on_payment_failure' );

function my_pmpro_restore_on_payment_success( $order ) {

	$user_id = (int) $order->user_id;

	$original_id = (int) get_user_meta(
		$user_id,
		MY_PMPRO_ORIGINAL_LEVEL_META_KEY,
		true
	);

	if ( empty( $user_id ) || empty( $original_id ) ) {
		return;
	}

	$current_level = pmpro_getMembershipLevelForUser( $user_id );

	if ( ! empty( $current_level ) && (int) $current_level->id === $original_id ) {
		delete_user_meta( $user_id, MY_PMPRO_ORIGINAL_LEVEL_META_KEY );
		return;
	}

	$changed = pmpro_changeMembershipLevel( $original_id, $user_id );

	if ( false !== $changed ) {
		delete_user_meta( $user_id, MY_PMPRO_ORIGINAL_LEVEL_META_KEY );
	}
}
add_action( 'pmpro_subscription_payment_completed', 'my_pmpro_restore_on_payment_success' );