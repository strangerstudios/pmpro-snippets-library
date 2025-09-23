<?php
/**
 * Restrict checkout if a valid zip code is not provided when using the Mailing Address Add On.
 *
 * title: Restrict your Membership Sites Mailing Locations by Specifying Valid Zip Codes
 * layout: snippet
 * collection: add-ons, pmpro-shipping
 * category: checkout, registration-check
 * link: https://www.paidmembershipspro.com/restrict-your-membership-sites-shipping-locations-by-specifying-valid-zip-codes/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_valid_mailing_zip_codes( $pmpro_continue_registration ) {
	// Check if things aren't okay, if not, lets not run this code further.
	if ( ! $pmpro_continue_registration ) {
		return $pmpro_continue_registration;
	}

	// Create an array of zip codes that are valid.
	$valid_zip_codes = array( '1234', '5678', '1448' );

	// Get the submitted mailing zip code.
	$mailing_zip_code = isset( $_REQUEST['pmpro_szipcode'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['pmpro_szipcode'] ) ) : '';

	// If a zip code was submitted and it's not valid, stop registration and show an error.
	if ( ! empty( $mailing_zip_code ) && ! in_array( $mailing_zip_code, $valid_zip_codes ) ) {
		pmpro_setMessage( 'Sorry, we do not ship to that area. Please choose an alternative mailing address.', 'pmpro_error' );
		$pmpro_continue_registration = false;
	} else {
		$pmpro_continue_registration = true;
	}

	return $pmpro_continue_registration;
}
add_filter( 'pmpro_registration_checks', 'my_pmpro_valid_mailing_zip_codes' );
