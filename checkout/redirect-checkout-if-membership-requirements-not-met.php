<?php
/**
 * Redirect away from the checkout page if the user doesn't have a required level.
 *
 * title: Redirect away from the checkout page if the user doesn't have a required level.
 * layout: snippet-example
 * collection: checkout
 * category: checkout
 * link: https://www.paidmembershipspro.com/redirect-away-from-checkout-if-user-doesnt-meet-membership-requirements/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_redirect_checkout_if_membership_requirements_not_met() {
	// Is PMPro active and are we on the checkout page?
	if ( ! function_exists( 'pmpro_is_checkout' ) || ! pmpro_is_checkout() ) {
		return;
	}

	// Get checkout level object.
	$checkout_level = pmpro_getLevelAtCheckout();

	if ( $checkout_level->id == 2 && ! pmpro_hasMembershipLevel( 1 ) ) {  // Change ids checked here.
		wp_redirect( site_url( 'page-explaining-you-need-level-1-first' ) );  // Change url here.
		exit;
	}
}
add_action( 'template_redirect', 'my_pmpro_redirect_checkout_if_membership_requirements_not_met' );
