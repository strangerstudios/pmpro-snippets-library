<?php
/**
 * Adjust the membership price at checkout for existing members upgrading to a specific level.
 *
 * title: Adjust Pricing for Existing Members Upgrading to a Level
 * layout: snippet-example
 * collection: checkout
 * category: pricing
 * link: https://www.paidmembershipspro.com/offer-members-a-discounted-rate-for-upgrading-to-a-new-level/
 *
 * This example checks whether the current user already has a specific membership level,
 * and if they are checking out for a different level, it modifies the price shown at checkout.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function pmpro_adjust_price_for_members_upgrading( $level ) {

	// If the user currently has level 1 and is upgrading to level 2...
	if ( pmpro_hasMembershipLevel( 1 ) && (int) $level->id === 2 ) {

		// Change the initial checkout amount.
		$level->initial_payment = 25.00;

		/**
		 * Optional: If level 2 is a recurring membership level and you want to adjust the recurring pricing,
		 * uncomment and update the billing details below.
		 */
		// $level->billing_amount = 50.00;
		// $level->cycle_number   = 1;
		// $level->cycle_period   = 'Month';
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'pmpro_adjust_price_for_members_upgrading' );`