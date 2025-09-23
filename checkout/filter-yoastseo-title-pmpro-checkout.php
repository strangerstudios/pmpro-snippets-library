<?php
/**
 * Add custom meta for the Membership Checkout page when using Yoast SEO.
 *
 * title: Filter the Membership Checkout wp_title using Yoast SEO
 * layout: snippet
 * collection: checkout
 * category: seo, meta
 * link: https://www.paidmembershipspro.com/filter-membership-checkout-page-title-meta-description-using-yoast-seo/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

//Update line 21 below to your preferred dynamic page title
function my_pmpro_filter_wpseo_title($title) {
	global $pmpro_pages, $pmpro_level;
    if( is_page($pmpro_pages['checkout']) ) {
		$title = $pmpro_level->name . ': Complete Your Membership Checkout';
    }
    return $title;
}
add_filter('wpseo_title', 'my_pmpro_filter_wpseo_title');