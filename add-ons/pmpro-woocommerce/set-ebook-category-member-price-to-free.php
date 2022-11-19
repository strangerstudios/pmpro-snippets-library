<?php
/**
 * This recipe sets all products in the Ebooks category to free for logged in members.
 *
 * title: Set products in Ebook category to free for members only.
 * layout: snippet
 * collection: add-ons
 * category: pmpro-woocommerce
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmprowoo_get_membership_price_ebooks_category( $discount_price, $level_id, $original_price, $product ) {
	// Return early if the user does not have a membership level.
	// Adjust this or add additional checks to require a specific membership level.
	if ( function_exists( 'pmpro_hasMembershipLevel' ) && ! pmpro_hasMembershipLevel() ) {
		return $discount_price;
	}

	// Check if the product is in the Ebook category and set price to 0 for all members.
	if ( has_term( array( 'ebooks' ), 'product_cat', $product->get_id() ) ) {
		$discount_price = 0;
	}

	// Return the discounted price.
    return $discount_price;
}
add_filter( 'pmprowoo_get_membership_price', 'my_pmprowoo_get_membership_price_ebooks_category', 10, 4 );
