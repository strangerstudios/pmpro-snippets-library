<?php
/**
 * This recipe sets all products in the Ebooks category to free for logged in members.
 * Learn more at https://www.paidmembershipspro.com/woocommerce-specific-category-free-for-members/
 *
 * title: Set products in Ebook category to free for members only.
 * layout: snippet
 * collection: add-ons
 * category: pmpro-woocommerce
 * link: https://www.paidmembershipspro.com/woocommerce-specific-category-free-for-members/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmprowoo_get_membership_price_ebooks_category( $discount_price, $level_id, $original_price, $product ) {
	// Get all membership levels for the current user and extract their IDs into an array.
	$user_levels = pmpro_getMembershipLevelsForUser( get_current_user_id() );
	$level_ids   = ! empty( $user_levels ) && is_array( $user_levels ) ? wp_list_pluck( $user_levels, 'id' ) : array();

	// Return early if the user does not have a membership level.
	if ( empty( $level_ids ) ) {
		return $discount_price;
	}

	// Uncomment to require a specific membership level.
	// if ( ! in_array( 1, $level_ids ) ) {
	// 	return $discount_price;
	// }

	// Set array of categories that are "free". Add additional category slugs as needed.
	$free_product_cats = array( 'ebooks' );

	// Check if the product is in the free categories
	if ( has_term( $free_product_cats, 'product_cat', $product->get_id() ) ) {
		$discount_price = 0;
	}

	// Return the discounted price.
	return $discount_price;
}
add_filter( 'pmprowoo_get_membership_price', 'my_pmprowoo_get_membership_price_ebooks_category', 10, 4 );
