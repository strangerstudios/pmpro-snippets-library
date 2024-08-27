<?php
/**
 * Allow expiring members to extend their membership on renewal or level change
 *
 * Extend the membership expiration date for a member with remaining days on their current level when they complete checkout for another level in the same level group that has an expiration date. Always add remaining days to the enddate.
 *
 * title: Allow expiring members to extend their membership on renewal or level change
 * layout: snippet
 * collection: checkout
 * category: renewals
 * url: https://www.paidmembershipspro.com/allow-members-purchase-membership-extension/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_checkout_level_extend_memberships( $level ) {
	global $current_user;

	// Return if the level being purchased does not have an expiration date.
	if ( empty( $level->expiration_number ) ) {
		return $level;
	}

	// Return if the user already has the level being purchased. Core PMPro will handle the renewal.
	if ( pmpro_hasMembershipLevel( $level->id ) ) {
		return $level;
	}

	// Check if user has a fixed-term level that will be replaced by the fixed-term level being purchased.
	$group_id = pmpro_get_group_id_for_level( $level->id );
	$group = pmpro_get_level_group( $group_id );
	if ( ! empty( $group ) && empty( $group->allow_multiple_selections ) ) {
		// Get all of the user's current membership levels.
		$my_levels = pmpro_getMembershipLevelsForUser( $current_user->ID );

		// Loop through the levels and see if any are in the same group as the level being purchased.
		if ( ! empty( $my_levels ) ) {
			foreach ( $my_levels as $my_level ) {

				// If this level is not in the same group, continue.
				if ( pmpro_get_group_id_for_level( $my_level->id ) != $group_id ) {
					continue;
				}

				// If we made it this far, the user is going to lose this level after checkout. Does it have an expiration date?
				if ( empty( $my_level->enddate ) ) {
					continue;
				}

				// Get the current end date of their membership.
				$expiration_date = $my_level->enddate;
			}
		}
	}

	// The user does not have a level in this group with an expiration date.
	if ( empty( $expiration_date ) ) {
		return $level;
	}

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

	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_checkout_level_extend_memberships' );
