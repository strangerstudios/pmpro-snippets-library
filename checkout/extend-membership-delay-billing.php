<?php
/**
 * Preserve membership dates into recurring renewal
 *
 * When a member with a one-time or fixed-term membership renews into a recurring subscription
 * for the same level, skip the initial payment and set the first recurring payment to begin
 * when their current expiration date ends.
 *
 * title: Preserve membership dates into recurring renewal
 * layout: snippet
 * collection: checkout
 * category: renewals
 * link: https://www.paidmembershipspro.com/keep-existing-expiration-date-renewal/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_checkout_level_profile_start_date_from_expiration( $level ) {
	// Only adjust recurring levels.
	if ( ! function_exists( 'pmpro_isLevelRecurring' ) || ! pmpro_isLevelRecurring( $level ) ) {
		return $level;
	}

	$user_id       = get_current_user_id();
	$current_level = pmpro_getMembershipLevelForUser( $user_id, $level->id );

	// If the user has this level and it ends in the future, defer the start.
	if ( $current_level && ! empty( $current_level->enddate ) ) {
		$now_gmt = current_time( 'timestamp', true );

		if ( intval( $current_level->enddate ) > $now_gmt ) {
			$new_start_date = gmdate( 'Y-m-d', intval( $current_level->enddate ) );
			$level->profile_start_date = $new_start_date;

			// They've already paid through that date; make today's charge $0.
			$level->initial_payment = 0;
		}
	}
	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_checkout_level_profile_start_date_from_expiration', 15 );

// Adjust the level cost text, to make it clear when the subscription will start.
function my_pmpro_checkout_cost_text_with_deferred_start( $cost_text, $level, $tags, $short ) {
	// Only affect the front-end Checkout page.
	if ( function_exists( 'pmpro_is_checkout' ) && ! pmpro_is_checkout() ) {
		return $cost_text;
	}

	// Must be a recurring level.
	if ( ! pmpro_isLevelRecurring( $level ) ) {
		return $cost_text;
	}
	
	// Get the level cycle period and cycle number and convert it to a Y-m-d to compare with the profile_start_date
	$level_expected_start_date = date( 'Y-m-d', strtotime( '+' . $level->cycle_number . ' ' . $level->cycle_period, current_time( 'timestamp', true ) ) );
	if ( $level->profile_start_date <= $level_expected_start_date ) {
		$cost_text .= ' Your subscription will start on ' . date( 'F j, Y', strtotime( $level->profile_start_date ) );
	}

	return $cost_text;
}
add_filter( 'pmpro_level_cost_text', 'my_pmpro_checkout_cost_text_with_deferred_start', 20, 4 );
