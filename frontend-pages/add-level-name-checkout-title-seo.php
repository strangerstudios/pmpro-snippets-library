<?php
/**
 * Add Level Name to the_title() Output on Membership Checkout Page for SEO
 *
 * title: Add Level Name to the_title() Output on Membership Checkout Page for SEO
 * layout: snippet
 * collection: frontend-pages
 * category: seo
 * url: https://www.paidmembershipspro.com/add-level-name-to-membership-checkout-page-title-for-seo/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// add level name to the_title() output on membership checkout page for SEO
function my_pmpro_membership_checkout_page_title( $title, $id ) {
	global $pmpro_pages;
	if ( ! empty( $pmpro_pages ) && is_page( $pmpro_pages['checkout'] ) && in_the_loop() ) {
		global $pmpro_level;
		$title .= ': ' . $pmpro_level->name;
	}
	return $title;
}
add_filter( 'the_title', 'my_pmpro_membership_checkout_page_title', 10, 2 );
