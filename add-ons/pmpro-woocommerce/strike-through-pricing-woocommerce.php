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
	// Let's not do this in the admin area, if PMPro is not active, or if the user does not have a membership level.
	if ( is_admin() || ! function_exists( 'pmpro_hasMembershipLevel' ) || ! pmpro_hasMembershipLevel() ) {
		return $price;
	}

	$formatted_price = ''; // Define the new variable.

	// If the person does not have a membership level, return the 'default' price.
	$level_id = isset( $current_user->membership_level->id ) ? $current_user->membership_level->id : null;
	if ( empty( $level_id ) ) {
		return $price;
	}

	// Get pricing for simple product.
	if ( $product->is_type( 'simple' ) ) {
		// Get the membership price and calculate the discount. 
		$regular_price = ! empty( $product->get_sale_price() ) ? $product->get_sale_price() : $product->get_regular_price();
		$member_price = pmprowoo_get_membership_price( $regular_price, $product );

		// Only show this to members and if the price isn't already the same as regular price.
		if ( isset( $level_id ) && floatval( $member_price ) !== floatval( $regular_price ) ) {
			$formatted_price = '<del>' . wc_price( $regular_price ) . '</del> ' . wc_price( $member_price );
			$price = $formatted_price;
		}
	}

	// Get pricing for variation/variable product.
	if ( $product->is_type( 'variable' ) ) {
		$regular_prices = array();
		$member_prices  = array();

		// Loop through all variations to figure out the price ranges - useful for individual variation pricing discounts.
		foreach ( $product->get_children() as $child_id ) {
			$variation = wc_get_product( $child_id );

			if ( ! $variation instanceof WC_Product || ! $variation->is_purchasable() ) {
				continue;
			}

			// Try to get the default product price, and fall back to the regular price if no sale price is set.
			$regular_price = ! empty( $variation->get_sale_price() ) ? $variation->get_sale_price() : $variation->get_regular_price();
			if ( empty( $regular_price ) ) {
				continue;
			}

			$member_price = pmprowoo_get_membership_price( $regular_price, $variation );

			$regular_prices[] = floatval( $regular_price );
			$member_prices[]  = floatval( $member_price );
		}

		// Let's compare arrays now to build the member prices.
		if ( ! empty( $regular_prices ) && ! empty( $member_prices ) ) {
			$min_reg = min( $regular_prices );
			$max_reg = max( $regular_prices );
			$min_mem = min( $member_prices );
			$max_mem = max( $member_prices );

			// Bail if the prices are identical.
			if ( $min_reg === $min_mem && $max_reg === $max_mem ) {
				return $price; // No need to strike through if the prices are the same.
			}

			// Figure out the regular price range.
			if ( $min_reg == $max_reg ) {
				// If the min and max are the same, just show one price.
				$regular_range = wc_price( $max_reg );
			} else {
				// If the min and max are different, show a range.
				$regular_range = wc_format_price_range( $min_reg, $max_reg );
			}
			
			// Figure out the member price range.
			if ( $min_mem == $max_mem ) {
				$member_range = wc_price( $max_mem );
			} else {
				$member_range  = wc_format_price_range( $min_mem, $max_mem );
			}

			// If the ranges differ, let's strike through the regular price and show the member price.
			if ( $regular_range !== $member_range ) {
				$price = '<del>' . $regular_range . '</del> ' . $member_range;
			}
		}
	}

	return $price;
}
add_filter( 'woocommerce_get_price_html', 'my_pmprowoo_strike_prices', 10, 2 );

/**
 * Show the same strikethrough values on the cart page
 */
function my_pmprowoo_strike_cart_price( $price, $cart_item, $cart_item_key ) {
	global $pmprowoo_member_discounts, $current_user;

	if ( ! function_exists( 'pmpro_hasMembershipLevel' ) || ! pmpro_hasMembershipLevel() ) {
		return $price;
	}

	$product = $cart_item['data'];

	// If no level is found, return the default price.
	$level_id = isset( $current_user->membership_level->id ) ? $current_user->membership_level->id : null;
	if ( empty( $level_id ) ) {
		return $price;
	}

	if ( $product->is_type( 'simple' ) || $product->is_type( 'variation' ) ) {
		$regular_price = $product->get_regular_price();
		$sale_price    = $product->get_sale_price();

		// Get the membership price and calculate the discount. 
		$default_price = ! empty( $sale_price ) ? $sale_price : $regular_price;
		$member_price = pmprowoo_get_membership_price( $default_price, $product );

		if ( isset( $level_id ) && floatval($member_price) !== floatval($default_price) ) {
			$price = '<del>' . wc_price( $default_price ) . '</del> ' . wc_price( $member_price );
		}
	}

	return $price;
}
add_filter( 'woocommerce_cart_item_price', 'my_pmprowoo_strike_cart_price', 10, 3 );