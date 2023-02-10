<?php
/**
 * This recipe ensures that only products that are NOT on sale will be discounted
 * when using the Woocommerce Add On. 
 * All other products will be discounted as expected.
 * 
 * Learn more at https://www.paidmembershipspro.com/woocommerce-specific-category-free-for-members/
 *
 * title: Only discounts regular priced items, excludes any items that are on sale
 * layout: snippet
 * collection: add-ons
 * category: pmpro-woocommerce
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function mypmpro_discount_sales( $discount_price, $lowest_price_level, $price, $product ) {

	$regular_price = $product->get_regular_price();
	$sale_price = $product->get_sale_price();

	if( empty( $sale_price ) ) {
		//Its not on sale, so we can discount it
		return $discount_price;
	}

	//On sale, dont discount it further
	return $sale_price;

}
add_filter( 'pmprowoo_get_membership_price', 'mypmpro_discount_sales', 10, 4 );