<?php

/**
 * Display credit card logos before the submit button on the Paid Memberships Pro Membership Checkout page.
 * 
 * title: Add Credit Cards Logos to Membership Checkout
 * layout: snippet
 * collection: checkout
 * category: logo
 * url: https://www.paidmembershipspro.com/add-credit-card-and-paypal-logos-to-checkout/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


 function pmpro_add_my_logos_to_checkout(){
	global $pmpro_level;

	if ( ! pmpro_isLevelFree( $pmpro_level ) ){
		echo '<h2>Accepted Credit Cards</h2>';
		echo '<img alt="Credit card logos for Visa, Mastercard, Discover, and American Express" style="max-width: 300px;" src="' . plugins_url( '/images/pay-with-credit-cards.png', __FILE__ ) .'" />';
	}
}
add_action( 'pmpro_checkout_before_submit_button', 'pmpro_add_my_logos_to_checkout', 10 );
