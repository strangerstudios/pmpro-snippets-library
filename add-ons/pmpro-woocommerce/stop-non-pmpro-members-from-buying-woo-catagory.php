<?php
/**
 * This recipe will stop non-members from purchasing products by category
 * Can purchase if they have an active Paid Memberships Pro Level
 * 
 * Learn more at https://www.paidmembershipspro.com/require-membership-to-purchase-specific-categories-of-products-in-woocommerce/
 *
 * title: Remove Add to Cart for specific product categories
 * layout: snippet
 * collection: add-ons
 * category: pmpro-woocommerce
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function stop_non_pmpro_members_from_buying_woo( $is_purchasable, $product ) {

	// Check if the user has an active membership level.
	if ( pmpro_hasMembershipLevel() ) {
		return $is_purchasable;
	}

	// Adjust the restricted categories that require membership level to purchase. Use category-slug.
	$restricted_category_slugs = array(
		'category-1', // Change to your category slugs
		'category-2', // Change to your category slugs or remove if not needed
		'category-3' // Change to your category slugs or remove if not needed
	);

	if ( has_term( $restricted_category_slugs, 'product_cat', $product->id ) ) {
		$is_purchasable = false;
	}

	return $is_purchasable;

}
add_filter( 'woocommerce_is_purchasable', 'stop_non_pmpro_members_from_buying_woo', 10, 2 );
