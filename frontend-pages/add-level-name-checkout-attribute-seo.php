<?php
/**
 * Add Level Name to the Membership Checkout <title> Attribute for SEO
 *
 * title: Add Level Name to Membership Checkout Attribute for SEO
 * layout: snippet
 * collection: frontend-pages
 * category: seo
 * link: https://www.paidmembershipspro.com/add-level-name-to-membership-checkout-page-title-for-seo/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_membership_checkout_title_parts( $title_parts ) {
	global $pmpro_pages, $pmpro_level;

	if ( ! empty( $pmpro_pages ) && is_page( $pmpro_pages['checkout'] ) && ! empty( $pmpro_level ) ) {
		$title_parts['title'] = $pmpro_level->name . ' – ' . $title_parts['title'];
	}

	return $title_parts;
}
add_filter( 'document_title_parts', 'my_pmpro_membership_checkout_title_parts', 20 );
