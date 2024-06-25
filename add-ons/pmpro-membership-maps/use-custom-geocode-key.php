<?php
/**
 * Override the Geocoding API key to a private key. This helps if you want to restrict
 * usage of the Geocoding API key. This key is not public like the JS Map API Key.
 * 
 * title: Override Geocoding API key
 * layout: snippet-example
 * collection: add-ons, pmpro-membership-maps
 * category: geocoding, google-maps
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_override_geocoding_api_key( $key ){

	$key = "NEW_KEY_HERE"; //Insert your key here.

	return $key;

}
add_filter( 'pmpromm_geocoding_api_key', 'my_pmpro_override_geocoding_api_key', 10, 1 );