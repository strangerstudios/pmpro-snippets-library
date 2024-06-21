<?php

/**
 * This recipe ensures that only specific levels can access the WooCommerce shop
 * Requires the PMPro WooCommerce Add-On: https://www.paidmembershipspro.com/add-ons/pmpro-woocommerce/
 * 
 * Update line 26 with the membership level IDs to allow into the store.
 * 
 * title: allow only specific membership levels to access the woocommerce shop
 * layout: snippet
 * collection: add-ons
 * category: pmpro-woocommerce
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_members_only_woocommerce_shop() {
	// Make sure WooCommerce is installed.
	if ( ! function_exists( 'is_woocommerce') ) {
		return;
	}

	// If we're not on the WC cart or checkout page, we don't want to redirect.
	if ( ! ( is_woocommerce() || is_cart() || is_checkout() ) ) {
		return;
	}

	// If the user has one of the specified membership levels, we don't want to redirect.
	if ( pmpro_hasMembershipLevel( array( 1, 2 ) ) ) {
		return;
	}

	// The user doesn't have the specified membership levels. Redirect them to the levels page.
	wp_redirect( pmpro_url( 'levels' ) );
    exit;
}
add_action( 'template_redirect', 'my_pmpro_members_only_woocommerce_shop' );
