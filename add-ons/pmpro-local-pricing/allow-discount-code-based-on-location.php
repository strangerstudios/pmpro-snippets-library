<?php
/**
 * Restrict discount code usage by the visitor's detected location based on IP address and geographic location.
 *
 * title: Restrict discount code usage by the visitor's detected location
 * layout: snippet
 * collection: add-ons, pmpro-local-pricing
 * category: currencies, checkout, discount codes
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_local_pricing_discount_codes_by_country( $country_discount_codes ) {
    // Array of country codes and discount codes.
    $country_discount_codes = array(
        // 'CA' => 'CANADA10',
        // 'ZA' => 'LEKKER',
    );
    return $country_discount_codes;
}
add_filter( 'pmpro_local_pricing_discounted_countries', 'my_pmpro_local_pricing_discount_codes_by_country' );
