<?php
/**
 * Exclude specific WooCommerce product categories from membership discount pricing.
 *
 * title: Exclude WooCommerce Categories from Membership Discount
 * layout: snippet
 * collection: add-ons
 * category: pmpro-woocommerce, woocommerce
 * link: https://www.paidmembershipspro.com/exclude-certain-woocommerce-products-from-membership-discount/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_exclude_woocommerce_discounts_for_categories( $price, $level_id, $original_price, $product ) {

	// Array of categories to exclude (category slugs).
	$exclude_categories = array( 'category-1', 'category-2', 'category-3' );

	// Get the product ID for variations or simple products.
	$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

	// If the product is in any excluded category, return the original price (no discount).
	if ( has_term( $exclude_categories, 'product_cat', $product_id ) ) {
		$price = $original_price;
	}

	return $price;
}
add_filter( 'pmprowoo_get_membership_price', 'my_pmpro_exclude_woocommerce_discounts_for_categories', 10, 4 );
