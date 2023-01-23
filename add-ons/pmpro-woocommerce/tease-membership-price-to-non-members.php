<?php
/**
 * Tease the membership price when viewing a WooCommerce product as a non-member.
 * 
 * title: Tease membership product price to non-members.
 * layout: snippet-example
 * collection: pmpro-woocommerce
 * category: upsell, pricing, woocommerce
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmprowoo_tease_membership_price() {
	global $post, $pmprowoo_product_levels, $pmprowoo_member_discounts;

	/**
	 * If we get here, they aren't a member and they don't have a level in their cart.
	 * Show the price for level ID 1. Change this for your needs.
	 */
        $level_id = 1;

	// Quitely exit if PMPro isn't active.
	if ( ! defined( 'PMPRO_DIR' ) ) {
		return;
	}

	// Make sure the $post is a product object.
	$product = wc_get_product( $post );

	// No product? Return.
	if ( empty( $product ) ) {
		return;
	}
	
	// Are they already a member? Return.
	if ( pmpro_hasMembershipLevel() ) {
		return;
	}

	// Set up our price variables.
	$price = $product->get_price();

	// Get the level IDs that are sold as products and gather cart contents.
	$membership_product_ids = array_keys( $pmprowoo_product_levels );
	$items = is_object( WC()->cart ) ? WC()->cart->get_cart_contents() : array(); // items in the cart
	
	/**
	 * Search for any membership level products in the cart.
	 * If found, don't show this message.
	 */
	foreach ( $items as $item ) {
		if ( in_array( $item['product_id'], $membership_product_ids ) ) {
			return;
		}
	}
	
	$level_price = '_level_' . $level_id . '_price';
	// Use this level to get the price
	if ( isset( $level_price ) && $level_id ) {
		if ( $product->get_type() === 'variation' ) {
			$product_id = $product->get_parent_id(); //for variations	
		} else {
			$product_id = $product->get_id();
		}
		$level_price = get_post_meta( $product_id, $level_price, true );
		if ( ! empty( $level_price ) || $level_price === '0' || $level_price === '0.00' || $level_price === '0,00' ) {
			$discount_price = $level_price;
		} elseif ( ! empty( $pmprowoo_member_discounts ) && ! empty( $pmprowoo_member_discounts[ $level_id ] ) ) {
			// Apply discounts if there are any for this level.
			$discount_price = $price - ( $price * $pmprowoo_member_discounts[ $level_id ] );
		}
	}

	// If we have a discount price, show the message.
	if ( ! empty( $discount_price ) ) {
		echo '<p><em><a href="' . pmpro_url( 'levels' ) . '">Members</a> pay: ' . wc_price( $discount_price ) . '</em></p>';
	}
}
add_action( 'woocommerce_single_product_summary', 'my_pmprowoo_tease_membership_price', 11 );
