<?php
/**
 * Add strike through pricing if the membership pricing is available for currrent user viewing Woo store.
 * 
 * title: Strike through pricing WooCommerce
 * layout: snippet
 * collection: add-ons, pmpro-woocommerce
 * category: woocommerce, pricing, UI
 * link: https://www.paidmembershipspro.com/strike-through-pricing-woocommerce/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmprowoo_strike_prices( $price, $product ) {
	global $pmprowoo_member_discounts, $current_user;

	if (
		is_admin() ||
		! function_exists( 'pmpro_hasMembershipLevel' ) ||
		! pmpro_hasMembershipLevel()
	) {
		return $price;
	}

	$level_id = isset( $current_user->membership_level->id ) ? $current_user->membership_level->id : null;

	// Handle Simple Products.
	if ( $product->is_type( 'simple' ) ) {
		$regular_price = $product->get_regular_price();
		$sale_price    = $product->get_sale_price();
		$base_price    = ! empty( $sale_price ) ? $sale_price : $regular_price;
		$member_price  = pmprowoo_get_membership_price( $base_price, $product );

		if ( isset( $level_id ) && floatval( $member_price ) !== floatval( $regular_price ) ) {
			$price = '<del>' . wc_price( $regular_price ) . '</del> ' . wc_price( $member_price );
		} else {
			$price = wc_price( $member_price );
		}
	}

	// Handle Variable Products.
	if ( $product->is_type( 'variable' ) ) {
		$regular_prices = array();
		$member_prices  = array();

		foreach ( $product->get_children() as $child_id ) {
			$variation = wc_get_product( $child_id );

			if ( ! $variation instanceof WC_Product || ! $variation->is_purchasable() ) {
				continue;
			}

			$regular_price = $variation->get_regular_price();

			if ( empty( $regular_price ) ) {
				continue;
			}

			$member_price = pmprowoo_get_membership_price( $regular_price, $variation );

			$regular_prices[] = floatval( $regular_price );
			$member_prices[]  = floatval( $member_price );
		}

		if ( ! empty( $regular_prices ) && ! empty( $member_prices ) ) {
			$min_reg = min( $regular_prices );
			$max_reg = max( $regular_prices );
			$min_mem = min( $member_prices );
			$max_mem = max( $member_prices );

			$regular_range = wc_format_price_range( $min_reg, $max_reg );
			$member_range  = wc_format_price_range( $min_mem, $max_mem );

			if ( $regular_range !== $member_range ) {
				$price = '<del>' . $regular_range . '</del> ' . $member_range;
			} else {
				$price = $member_range;
			}
		}
	}

	return $price;
}
add_filter( 'woocommerce_get_price_html', 'my_pmprowoo_strike_prices', 10, 2 );
add_filter( 'woocommerce_variation_price_html', 'my_pmprowoo_strike_prices', 10, 2 );

/**
 * Show the same strike-through pricing in the WooCommerce cart.
 */
function my_pmprowoo_strike_cart_price( $price, $cart_item, $cart_item_key ) {
	global $current_user;

	if (
		! function_exists( 'pmpro_hasMembershipLevel' ) ||
		! pmpro_hasMembershipLevel()
	) {
		return $price;
	}

	$product  = $cart_item['data'];
	$level_id = isset( $current_user->membership_level->id ) ? $current_user->membership_level->id : null;

	if ( empty( $level_id ) ) {
		return $price;
	}

	if ( $product->is_type( 'simple' ) || $product->is_type( 'variation' ) ) {
		$regular_price = $product->get_regular_price();
		$member_price  = pmprowoo_get_membership_price( $regular_price, $product );

		if ( floatval( $member_price ) !== floatval( $regular_price ) ) {
			$price = '<del>' . wc_price( $regular_price ) . '</del> ' . wc_price( $member_price );
		} else {
			$price = wc_price( $member_price );
		}
	}

	return $price;
}
add_filter( 'woocommerce_cart_item_price', 'my_pmprowoo_strike_cart_price', 10, 3 );
