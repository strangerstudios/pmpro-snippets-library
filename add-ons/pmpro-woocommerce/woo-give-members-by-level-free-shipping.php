<?php
/**
 * Show Free Shipping only to specific Paid Memberships Pro levels.
 * Change level ID on line 22 to the ID(s) of the level(s) you want to receive free shipping.
 *
 * title: Free Shipping for Specific Membership Levels
 * layout: snippet
 * collection: woocommerce
 * category: shipping
 * link: https://www.paidmembershipspro.com/give-members-free-shipping-at-shop-checkout/
 *
 * This snippet makes the WooCommerce Free Shipping method available
 * only to members of specific Paid Memberships Pro levels.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmprowc_free_shipping( $rates, $package ) {
	// Membership level IDs that should receive Free Shipping.
	$pmprowc_free_shipping_levels = array( 1, 2 );

	$free_shipping_key = '';

	// Find the Free Shipping rate key.
	foreach ( $rates as $key => $rate ) {
		if ( strpos( $key, 'free_shipping' ) !== false ) {
			$free_shipping_key = $key;
			break;
		}
	}

	// If no Free Shipping method exists, return rates unchanged.
	if ( empty( $free_shipping_key ) ) {
		return $rates;
	}

	// Check membership and adjust available shipping methods.
	if ( function_exists( 'pmpro_hasMembershipLevel' ) && pmpro_hasMembershipLevel( $pmprowc_free_shipping_levels ) ) {
		// Keep only the Free Shipping method.
		$rates = array(
			$free_shipping_key => $rates[ $free_shipping_key ],
		);
	} else {
		// Remove Free Shipping for non-members.
		unset( $rates[ $free_shipping_key ] );
	}

	return $rates;
}
add_filter( 'woocommerce_package_rates', 'my_pmprowc_free_shipping', 10, 2 );