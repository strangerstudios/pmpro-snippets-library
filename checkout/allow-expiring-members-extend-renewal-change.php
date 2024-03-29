<?php
/**
 * Allow expiring members to extend their membership on renewal or level change
 *
 * Extend the membership expiration date for a member with remaining days on their current level when they complete checkout for ANY other level that has an expiration date. Always add remaining days to the enddate.
 *
 * title: Allow expiring members to extend their membership on renewal or level change
 * layout: snippet
 * collection: checkout
 * category: renewals
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


function my_pmpro_checkout_level_extend_memberships( $level ) {

	global $pmpro_msg, $pmpro_msgt, $current_user;

	// does this level expire? are they an existing members with an expiration date? are they not renewing their current level?
	if ( ! empty( $level ) && ! empty( $level->expiration_number ) && pmpro_hasMembershipLevel() && ! empty( $current_user->membership_level->enddate )  && $current_user->membership_level->ID !== $level->id ) {

		// get the current enddate of their membership
		$expiration_date = $current_user->membership_level->enddate;

		// calculate days left
		$todays_date = time();
		$time_left   = $expiration_date - $todays_date;

		// time left?
		if ( $time_left > 0 ) {

			// convert to days and add to the expiration date (assumes expiration was 1 year)
			$days_left = floor( $time_left / ( 60 * 60 * 24 ) );

			// figure out days based on period
			if ( $level->expiration_period == 'Day' ) {
				$total_days = $days_left + $level->expiration_number;
			} elseif ( $level->expiration_period == 'Week' ) {
				$total_days = $days_left + $level->expiration_number * 7;
			} elseif ( $level->expiration_period == 'Month' ) {
				$total_days = $days_left + $level->expiration_number * 30;
			} elseif ( $level->expiration_period == 'Year' ) {
					$total_days = $days_left + $level->expiration_number * 365;
			}

			// update number and period
			$level->expiration_number = $total_days;
			$level->expiration_period = 'Day';
		}
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_checkout_level_extend_memberships' );
