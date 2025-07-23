<?php
/**
 * Show a Different “Terms of Service” at Checkout Based on Membership Level
 *
 * title: Show a Different TOS at Checkout Based on Membership Level
 * layout: snippet
 * collection: checkout
 * category: tos, levels
 * link:https://www.paidmembershipspro.com/show-different-terms-service-checkout-based-membership-level/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


/**
 * Change the Terms of Service page based on the membership level.
 */
function my_option_pmpro_tospage( $tospage ) {
	// Only run this logic on the checkout page.
	if ( ! function_exists( 'pmpro_is_checkout' ) || ! pmpro_is_checkout() ) {
		return $tospage;
	}

	// Get level object at checkout.
	$level = pmpro_getLevelAtCheckout();

	// If the level at checkout is 1, use the specific TOS page.
	if ( ! empty( $level ) && $level->id == 1 ) {
		$tospage = 27; // Page ID for Level 1 TOS.
	}

	// For all other levels, we don't do anything and return the default TOS page ID.
	return $tospage;
}
add_filter( 'option_pmpro_tospage', 'my_option_pmpro_tospage' );
