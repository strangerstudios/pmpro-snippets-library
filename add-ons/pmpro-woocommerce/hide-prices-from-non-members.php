<?php
/**
 * This recipe hides prices + add to cart for non members in Paid Memberships Pro.
 * Requires the PMPro WooCommerce Add-On: https://www.paidmembershipspro.com/add-ons/pmpro-woocommerce/
 * Learn more at https://www.paidmembershipspro.com/turn-your-woocommerce-store-into-a-catalog-for-non-members/
 *
 * title: hides prices + add to cart from non-members
 * layout: snippet
 * collection: add-ons
 * category: pmpro-woocommerce
 * link: https://www.paidmembershipspro.com/turn-your-woocommerce-store-into-a-catalog-for-non-members/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function remove_my_woo_prices( $price, $product ) {
 global $pmprowoo_product_levels;

	//no product levels or PMProWC not active
	if ( empty( $pmprowoo_product_levels ) ) {
		return '';
	}

	//check if the product is a membership level
	$product_ids = array_keys( $pmprowoo_product_levels );
	if ( ! in_array( $product->get_id(), $product_ids ) ) {
		return '';
	}

	return $price;
}

function hide_prices_for_non_pmpro_members(){
  
 	//if user has a PMPro membership level simply return.
	if ( pmpro_hasMembershipLevel() ) {
		return;
	}

	//set price of all products to NULL
	add_filter( 'woocommerce_variable_sale_price_html', 'remove_my_woo_prices', 10, 2 );
	add_filter( 'woocommerce_variable_price_html', 'remove_my_woo_prices', 10, 2 );
	add_filter( 'woocommerce_get_price_html', 'remove_my_woo_prices', 10, 2 );

	//hide add to cart button *ALL INSTANCES*
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
	remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
	remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
	remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
	remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );

	//hide the sales badge
	add_filter('woocommerce_sale_flash', '__return_false');
}
add_action( 'wp', 'hide_prices_for_non_pmpro_members' );

function my_pmpro_redirect_users_away_from_woo() {
  
	//if user has a PMPro membership level simply return.
	if ( pmpro_hasMembershipLevel() ) {
		return;
	}
  
	//if the user ends up on the checkout or cart page, redirect to the home page.
	if ( is_checkout() || is_cart() ) {
		wp_redirect( home_url() ); //change this to another URL if needed.
		exit;
	}
}
add_action( 'template_redirect', 'my_pmpro_redirect_users_away_from_woo' );
