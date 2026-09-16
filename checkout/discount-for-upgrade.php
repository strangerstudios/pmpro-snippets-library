<?php
/**
 * Adjust the membership price at checkout for existing members upgrading to a specific level.
 *
 * title: Adjust Pricing for Existing Members Upgrading to a Level
 * layout: snippet
 * collection: checkout
 * category: pricing
 * link: https://www.paidmembershipspro.com/offer-members-a-discounted-rate-for-upgrading-to-a-new-level/
 *
 * This example checks whether the current user already has a specific membership level,
 * and if they are checking out for a different level, it modifies the price shown at checkout.
 *
 * You can add this recipe to your site by creating a custom plugin.
 * Read this companion article for step-by-step directions:
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

		// Flag the level so the cost text below can explain the adjusted price.
		$level->pmpro_upgrade_price_applied = true;
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'pmpro_adjust_price_for_members_upgrading' );

/**
 * Show why the checkout price differs from the levels page.
 *
 * The pmpro_level_cost_text filter runs everywhere level pricing is displayed (levels page,
 * account page, emails, etc.), so we only add the note when the level object was actually
 * adjusted by the pmpro_checkout_level filter above.
 */
function pmpro_adjust_price_for_members_upgrading_cost_text( $text, $level ) {
	if ( ! empty( $level->pmpro_upgrade_price_applied ) ) {
		$text .= '<br><em>(' . esc_html__( 'Member upgrade price applied', 'paid-memberships-pro' ) . ')</em>';
	}

	return $text;
}
add_filter( 'pmpro_level_cost_text', 'pmpro_adjust_price_for_members_upgrading_cost_text', 10, 2 );
