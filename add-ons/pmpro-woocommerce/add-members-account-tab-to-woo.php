<?php
/**
 * This code registers a new "My Membership" endpoint within WooCommerce’s 
 * "My Account" section. It adds a corresponding menu item and displays 
 * PMPro account details using a shortcode.
 *
 * title: Add a PMPro Members Account Page tab to the WooCommerce Account Page Navigation
 * layout: snippet
 * collection: add-ons
 * category: pmpro-woocommerce, account-page, woocommerce
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function custom_add_my_account_endpoint() {
    add_rewrite_endpoint( 'my-membership', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'custom_add_my_account_endpoint' );

function custom_my_account_content() {
    // Directly output the shortcode without wrapping it in another 'woocommerce-MyAccount-content' div
    echo do_shortcode('[pmpro_account sections="membership"]');
}
add_action('woocommerce_account_my-membership_endpoint', 'custom_my_account_content');

function custom_my_account_menu_items( $items ) {
    $items['my-membership'] = 'My Membership'; // Change this text as needed
    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'custom_my_account_menu_items' );