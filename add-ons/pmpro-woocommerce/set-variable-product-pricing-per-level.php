<?php
/**
 * Use the pmprowoo_get_membership_price filter to set prices for variable products.
 * Update the $membership_prices array.
 * Each item in that array should have a key equal to the membership level id,
 * and a value equal to an array of the form array( {variable_product_id} => {member_price} )
 * Requires the PMPro WooCommerce Add-On: https://www.paidmembershipspro.com/add-ons/pmpro-woocommerce/
 * 
 * title: set custom pricing for variable products per level
 * layout: snippet
 * collection: add-ons
 * category: pmpro-woocommerce
 * link: https://www.paidmembershipspro.com/set-woocommerce-variable-product-prices-per-membership-level/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function mypmpro_member_pricing_for_variable_products( $discount_price, $level_id, $original_price, $product ) {

    // Skip if not a variation (optional: remove this if parent products too)
    if ( ! $product->is_type( 'variation' ) ) {
        return $discount_price;
    }
  
    $variation_id = $product->get_id();

// Define pricing by level and variation ID
    $membership_prices = array(
        1 => array(					    //-level 1 prices-
            1121 => 10.00,			//--product ID #1121 price for level 1
            1122 => 15.00,			//--product ID #1122 price for level 1
            1123 => 20.00,			//--product ID #1123 price for level 1
            1124 => 65.00,			//--product ID #1124 price for level 1
        ),
        3 => array(					    //-level 3 prices-
            1121 => 15.00,			//--product ID #1121 price for level 1
            1122 => 20.00,			//--product ID #1122 price for level 1
            1123 => 25.00,			//--product ID #1123 price for level 1
            1124 => 40.00,			//--product ID #1124 price for level 1
        ),
    );

    // Loop through each level
    foreach ( $membership_prices as $level => $product_prices ) {
        if ( pmpro_hasMembershipLevel( $level ) && isset( $product_prices[ $variation_id ] ) ) {
            return floatval( $product_prices[ $variation_id ] );
        }
    }

    // No match, return existing discounted or regular price
    return $discount_price;
}
add_filter( 'pmprowoo_get_membership_price', 'mypmpro_member_pricing_for_variable_products', 10, 4 );
