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
 * link: 
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_checkout_level_profile_start_date_from_expiration( $level ) {
	if ( ! pmpro_isLevelRecurring( $level ) ) {
		return $level;
	}

	$current_level = pmpro_getMembershipLevelForUser();
	if ( $current_level->id == $level->id ) {
		if ( ! empty( $current_level->enddate ) && $current_level->enddate > time() ) {
			global $my_pmpro_profile_start_date_from_expiration_flag;
			$my_pmpro_profile_start_date_from_expiration_flag = date( 'Y-m-d', $current_level->enddate );
			$level->initial_payment = 0;
		}
	}
	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_checkout_level_profile_start_date_from_expiration', 15, 1 );

function my_pmpro_profile_start_date_from_expiration( $date, $order ) {
	global $my_pmpro_profile_start_date_from_expiration_flag;
	if ( ! empty( $my_pmpro_profile_start_date_from_expiration_flag ) ) {
		$date = $my_pmpro_profile_start_date_from_expiration_flag;
	}
	return $date;
}
add_filter( 'pmpro_profile_start_date', 'my_pmpro_profile_start_date_from_expiration', 15, 2 );
