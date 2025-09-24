<?php
/**
 * Add custom title and meta description for the Membership Checkout page when using Yoast SEO.
 *
 * title: Filter the Membership Checkout Title and Meta Description using Yoast SEO
 * layout: snippet
 * collection: checkout
 * category: seo, title, meta
 * link: https://www.paidmembershipspro.com/filter-membership-checkout-page-title-meta-description-using-yoast-seo/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Update line 21 below to your preferred dynamic page title.
// you can remove one of the functions if you don't want to use it.
function my_pmpro_filter_wpseo_title($title) {
	global $pmpro_pages, $pmpro_level;
	if ( is_page( $pmpro_pages['checkout'] ) ) {
		$title = $pmpro_level->name . ': Complete Your Membership Checkout';
	}
	return $title;
}
add_filter( 'wpseo_title', 'my_pmpro_filter_wpseo_title' );

// Update line 19 below to your preferred dynamic page description.
function my_pmpro_filter_wpseo_metadesc($description) {
	global $pmpro_pages, $pmpro_level;
	if ( is_page( $pmpro_pages['checkout'] ) ) {
		$description = $pmpro_level->description;
	}
	return $description;
}
add_filter( 'wpseo_metadesc', 'my_pmpro_filter_wpseo_metadesc' );



