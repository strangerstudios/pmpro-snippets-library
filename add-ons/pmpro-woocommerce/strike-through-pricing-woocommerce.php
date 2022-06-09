<?php
/**
 * This will add strike through pricing if the membership pricing is available for currrent user viewing Woo store.
 * Add this code to your PMPro Customizations Plugin - https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmprowoo_strike_prices( $price, $product ) {
	global $pmprowoo_member_discounts, $current_user;

	// Let's not adjust things while in the admin area.
	if ( is_admin() ) {
		return $price;
	}

	$formatted_price = ''; // Define the new variable.
	$level_id = $current_user->membership_level->id;

	if ( empty( $level_id ) ) {
		return $price;
	}

	// get pricing for simple product
	if ( $product->is_type( 'simple' ) ) {
		// get normal non-member price.
		$regular_price = get_post_meta( $product->get_id(), '_regular_price', true );
		$sale_price    = get_post_meta( $product->get_id(), '_sale_price', true );

		// Get the membership price, this checks if the product has level pricing etc.
		if ( ! empty( $sale_price ) ) {
			$regular_price = $sale_price;
			$price         = pmprowoo_get_membership_price( $regular_price, $product );
		} else {
			$price = pmprowoo_get_membership_price( $regular_price, $product );
		}

		// only show this to members and if the price isn't already the same as regular price.
		if ( isset( $level_id ) && $price !== $regular_price ) {
			$formatted_price = '<del>' . wc_price( $regular_price ) . '</del> ';
		}

		$formatted_price .= wc_price( $price );
		// update price variable so we can return it later.
		$price = $formatted_price;
	}

	// get pricing for variable products.
	if ( $product->is_type( 'variable' ) ) {
		$prices        = $product->get_variation_prices( true );
		$min_price     = current( $prices['price'] );
		$max_price     = end( $prices['price'] );
		$regular_range = wc_format_price_range( $min_price, $max_price );
		if ( isset( $level_id ) && ! empty( $pmprowoo_member_discounts ) && ! empty( $pmprowoo_member_discounts[ $level_id ] ) ) {
			$formatted_price = '<del>' . $regular_range . '</del> ';
		}
		$formatted_price .= $price;
		$price            = $formatted_price;
	}

	return $price;
}
add_filter( 'woocommerce_get_price_html', 'my_pmprowoo_strike_prices', 10, 2 );
