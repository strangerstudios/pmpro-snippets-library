<?php
/**
 * Add Level Name to the Membership Checkout <title> Attribute for SEO
 *
 * title: Add Level Name to Membership Checkout Attribute for SEO
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
function my_pmpro_membership_checkout_head_title( $title, $sep ) {
	global $pmpro_pages;
	if ( ! empty( $pmpro_pages ) && is_page( $pmpro_pages['checkout'] ) ) {
		global $pmpro_level;
		$title = $pmpro_level->name . ' ' . $sep . ' ' . $title;
	}
	return $title;
}
add_filter( 'wp_title', 'my_pmpro_membership_checkout_head_title', 20, 2 );
