<?php
/**
 * Show today's date before the content of the Terms of Service page at Membership Checkout.
 *
 * title: Terms Of Service Add Today's Date
 * layout: snippet
 * collection: checkout
 * category: registration-check
 * url:https://www.paidmembershipspro.com/show-todays-date-in-the-terms-of-service-box-at-membership-checkout/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function show_today_date_before_pmpro_tos_content( $pmpro_tos_content, $pmpro_tos ) {
	$pmpro_tos_content = '<p>Today is ' . date( get_option( 'date_format' ) ) . '.</p>' . $pmpro_tos_content;
	return $pmpro_tos_content;
}
add_filter( 'pmpro_tos_content', 'show_today_date_before_pmpro_tos_content', 10, 2 );