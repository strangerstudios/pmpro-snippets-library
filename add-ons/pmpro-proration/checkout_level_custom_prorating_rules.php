<?php
/**
 * Replace the default proration rules in the Proration Add On
 *
 * title: Replace the default proration rules in the Proration Add On
 * layout: snippet
 * collection: add-ons, pmpro-proration
 * category: proration, checkout
 * link: https://www.paidmembershipspro.com/add-ons/proration-prorate-membership/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Swap the Add On's checkout filter for our own prorating function.
 *
 * Both Paid Memberships Pro and the Proration Add On must be active.
 *
 * @return void
function my_pmpro_init_custom_prorating_rules() {
	remove_filter( 'pmpro_checkout_level', 'pmprorate_pmpro_checkout_level', 10 );
	add_filter( 'pmpro_checkout_level', 'my_pmpro_checkout_level_custom_prorating_rules', 10 );
}
add_action( 'init', 'my_pmpro_init_custom_prorating_rules' );

/**
 * Set the initial payment at checkout using our own proration rules.
 *
 * Edit this function to prorate per your needs. There are three sections in the
 * if/elseif/else check below: downgrades, upgrades to a level with the same billing
 * period, and upgrades to a level with a different billing period. The code in each
 * section mirrors the Add On's default behavior, so change only the sections you
 * want to handle differently.
 *
 * Generally you should be setting the initial_payment value on the $level object and,
 * where the member's payment date needs to stay the same, hooking pmpro_profile_start_date.
 * Refer to the Add On code for the current default calculations.
 *
 * @param object $level The membership level being checked out for.
 * @return object
 */
function my_pmpro_checkout_level_custom_prorating_rules( $level ) {
	// We can only prorate if the user already has a membership level.
	if ( ! pmpro_hasMembershipLevel() ) {
		return $level;
	}

	global $current_user;
	$clevel = $current_user->membership_level;

	$morder = new MemberOrder();
	$morder->getLastMemberOrder( $current_user->ID, array( 'success', '', 'cancelled' ) );

	// No prorating needed if there is no order, e.g. an admin gave them the level.
	if ( empty( $morder->timestamp ) ) {
		return $level;
	}

	if ( pmprorate_isDowngrade( $clevel->id, $level->id ) ) {
		/*
		 * Downgrade rule in a nutshell:
		 * 1. Charge $0 now.
		 * 2. Allow their current membership to expire on their next payment date.
		 * 3. Set up the new subscription to start billing on that date.
		 * 4. Other code in the Add On handles changing the user's level on the future date.
		 */
		$level->initial_payment = 0;

		global $pmpro_checkout_old_level;
		$pmpro_checkout_old_level = $clevel;
	} elseif ( pmprorate_have_same_payment_period( $clevel->id, $level->id ) ) {
		/*
		 * Upgrade with the same billing period in a nutshell:
		 * 1. Calculate the initial payment to cover the remaining time in the current pay period.
		 * 2. Set up the subscription to start on the next payment date at the new rate.
		 */
		$payment_date      = pmprorate_trim_timestamp( $morder->timestamp );
		$next_payment_date = pmprorate_trim_timestamp( pmpro_next_payment( $current_user->ID ) );
		$today             = pmprorate_trim_timestamp( current_time( 'timestamp' ) );
		$days_in_period    = ceil( ( $next_payment_date - $payment_date ) / 3600 / 24 );

		// If there are no days in the period, the next payment should have happened already. Bail to avoid dividing by 0.
		if ( $days_in_period <= 0 ) {
			return $level;
		}

		$days_passed = ceil( ( $today - $payment_date ) / 3600 / 24 );
		$per_passed  = $days_passed / $days_in_period; // As a percentage (decimal).
		$per_left    = 1 - $per_passed;

		/*
		 * Now figure out how to adjust the price.
		 * (a) What they should pay for the new level = $level->billing_amount * $per_left.
		 * (b) What they should have paid for the current level = $clevel->billing_amount * $per_passed.
		 * What they need to pay = (a) + (b) - (what they already paid).
		 *
		 * A negative number would technically require a credit be given to the customer, but there
		 * is no easy way to do that across all gateways, so the cost is zeroed out instead.
		 */
		$new_level_cost = $level->billing_amount * $per_left;
		$old_level_cost = $clevel->billing_amount * $per_passed;

		$level->initial_payment = min( $level->initial_payment, round( $new_level_cost + $old_level_cost - $morder->subtotal, 2 ) );

		// Just in case we have a negative payment.
		if ( $level->initial_payment < 0 ) {
			$level->initial_payment = 0;
		}

		// Make sure the payment date stays the same.
		add_filter( 'pmpro_profile_start_date', 'pmprorate_set_startdate_to_next_payment_date', 10, 2 );
	} else {
		/*
		 * Upgrade with a different billing period in a nutshell:
		 * 1. Apply a credit to the initial payment based on the partial period of their old level.
		 * 2. The new subscription starts today and renews one new period from now.
		 */
		$payment_date      = pmprorate_trim_timestamp( $morder->timestamp );
		$next_payment_date = pmprorate_trim_timestamp( pmpro_next_payment( $current_user->ID ) );
		$today             = pmprorate_trim_timestamp( current_time( 'timestamp' ) );
		$days_in_period    = ceil( ( $next_payment_date - $payment_date ) / 3600 / 24 );

		// If there are no days in the period, the next payment should have happened already. Bail to avoid dividing by 0.
		if ( $days_in_period <= 0 ) {
			return $level;
		}

		$days_passed = ceil( ( $today - $payment_date ) / 3600 / 24 );
		$per_passed  = $days_passed / $days_in_period; // As a percentage (decimal).
		$per_left    = 1 - $per_passed;
		$credit      = $morder->subtotal * $per_left;

		$level->initial_payment = round( $level->initial_payment - $credit, 2 );

		// Just in case we have a negative payment.
		if ( $level->initial_payment < 0 ) {
			$level->initial_payment = 0;
		}
	}

	return $level;
}