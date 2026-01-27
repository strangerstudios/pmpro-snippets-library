<?php
/**
 * title: Let Member Change Payment Gateway for Active Subscription 
 * layout: snippet
 * collection: checkout
 * category: 
 * link: https://www.paidmembershipspro.com/change-subscription-payment-method/#members-experience
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Get the next payment date for a user's level inclusive of subscriptions and expiration dates.
 *
 * If the current user has a subscription for a passed level ID, return the next payment date for that subscription.
 * Otherwise, if the user has an expiration date set for the level, return that.
 * Otherwise, return null.
 *
 * @param int $level_id The level ID to check for.
 * @return int|null The next payment date/expiration date as a timestamp or null.
 */
function my_pmpro_renew_or_change_payment_method_get_next_payment_date( $level_id ) {
	global $current_user;

	// Bail if not logged in.
	if ( empty( $current_user->ID ) ) {
		return null;
	}

	// See if the user has a subscription or level for the passed level ID.
	$current_subscriptions = PMPro_Subscription::get_subscriptions_for_user( $current_user->ID, $level_id );
	$current_level = pmpro_getSpecificMembershipLevelForUser( $current_user->ID, $level_id );
	if ( ! empty( $current_subscriptions ) ) {
		// Return the next payment date for the subscription.
		$next_payment_date = $current_subscriptions[0]->get_next_payment_date();
	} elseif ( ! empty( $current_level ) ) {
		// Return the expiration date for the level.
		$next_payment_date = $current_level->enddate;
	}

	// If we do not have a next payment date, return null.
	if ( empty( $next_payment_date ) ) {
		return null;
	}

	// If we have a subscription start date in the past, return null.
	if ( $next_payment_date < current_time( 'timestamp' ) ) {
		return null;
	}

	// Ok, we can adjust the start date.
	return $next_payment_date;
}

/**
 * If checking out for same level with active membership, set initial payment to $0 and start subscription on next payment date OR expiration date.
 */
function my_pmpro_renew_or_change_payment_method_checkout_level( $level ) {
	global $current_user;

	// Return early if using a discount code
	if ( ! empty( $level->discount_code ) || ! empty( $_REQUEST['discount_code'] ) ) {
		return $level;
	}

	// Assume we do not need to adjust the checkout level.
	$subscription_start_date = my_pmpro_renew_or_change_payment_method_get_next_payment_date( $level->id );

	// If we do not have a subscription start date, return.
	if ( empty( $subscription_start_date ) ) {
		return $level;
	}

	// Ok, we can adjust the level.
	// Charge them nothing today.
	$level->initial_payment = 0;

	// Set the billing start date on the checkout level.
	$level->profile_start_date = date( 'Y-m-d H:i:s', $subscription_start_date );

	// Get their active or last active subscription.
	$last_subscription = PMPro_Subscription::get_subscription(
		array(
			'user_id' => $current_user->ID,
			'membership_level_id' => $level->id
		)
	);

	// If they have a last subscription, set the billing amount.
	// Optionally remove this line if you want to ignore legacy subscription pricing.
	if ( ! empty( $last_subscription ) ) {
		$level->billing_amount = $last_subscription->get_billing_amount();
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_renew_or_change_payment_method_checkout_level', 10 );

/**
 * Change the Level Cost Text on the checkout page to include the next payment date.
*/
function my_pmpro_renew_or_change_payment_method_level_cost_text( $cost, $level ) {
	global $pmpro_pages;

	// Bail if this is not the checkout page.
	if ( empty( $pmpro_pages ) || empty( $pmpro_pages['checkout'] ) || ! is_page( $pmpro_pages['checkout'] ) ) {
		return $cost;
	}

	// Return early if using a discount code
	if ( ! empty( $level->discount_code ) || ! empty( $_REQUEST['discount_code'] ) ) {
		return $cost;
	}

	// Bail if the level is not recurring.
	if ( ! pmpro_isLevelRecurring( $level ) ) {
		return $cost;
	}

	// Assume we do not need to adjust the cost text.
	$subscription_start_date = my_pmpro_renew_or_change_payment_method_get_next_payment_date( $level->id );

	// If we do not have a subscription start date, bail.
	if ( empty( $subscription_start_date ) ) {
		return $cost;
	}

	// Ok, we can adjust the cost text.
	$cost .= ' Your first subscription payment will be processed on ' . date_i18n( get_option( 'date_format' ), $subscription_start_date ) . '.';

	return $cost;
}
add_filter( 'pmpro_level_cost_text', 'my_pmpro_renew_or_change_payment_method_level_cost_text', 10, 2 );

/**
 * Show "Change Payment Method" in My Memberships only when the user has an active subscription with a next payment date in the future.
 *
 */
function my_pmpro_renew_or_change_payment_method_member_action_links( $pmpro_member_action_links, $level_id ) {
	global $current_user;

	$new_pmpro_member_action_links = array();

	// Get subscriptions tied to this user+level.
	$subscriptions = PMPro_Subscription::get_subscriptions_for_user( $current_user->ID, $level_id );

	if ( ! empty( $subscriptions ) ) {
		// Use the most recent/first subscription.
		$subscription = $subscriptions[0];

		// Pull details we care about.
		$next_payment_date = $subscription->get_next_payment_date();

		// Only show link if next payment date exists and is in the future.
		if ( ! empty( $next_payment_date ) && $next_payment_date > current_time( 'timestamp' ) ) {
			$new_pmpro_member_action_links['switch'] = sprintf(
				'<a id="pmpro_actionlink-switch" href="%s">%s</a>',
				esc_url( add_query_arg( 'pmpro_level', (int) $level_id, pmpro_url( 'checkout', '', 'https' ) ) ),
				esc_html__( 'Change Payment Method', 'pmpro' )
			);
		}
	}

	// Prepend our link (if any) to existing links.
	return array_merge( $new_pmpro_member_action_links, $pmpro_member_action_links );
}
add_filter( 'pmpro_member_action_links', 'my_pmpro_renew_or_change_payment_method_member_action_links', 10, 2 );
