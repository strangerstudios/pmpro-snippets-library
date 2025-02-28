<?php
/**
 * Restrict level signups by country
 *
 * title: Restrict level signups by country
 * layout: snippet
 * collection: checkout
 * category: registration-check
 *
 * This code snippet allow to restrict level signups by country.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function pmpro_restricted_checkout_by_country_init() {
	global $restricted_countries;

	// Specify country restrictions per level. Array keys are level IDs, values are arrays of country codes.
	$restricted_countries = array(
		1 => array( 'FR', 'IT' ),
		2 => array( 'IT' ),
	);
}
add_action( 'init', 'pmpro_restricted_checkout_by_country_init' );

function pmpro_registration_check_restricted_by_country( $value ) {
	global $restricted_countries, $pmpro_msg, $pmpro_msgt;

	$country = isset( $_REQUEST['bcountry'] ) ? sanitize_text_field( $_REQUEST['bcountry'] ) : '';
	$level   = pmpro_getLevelAtCheckout();

	if ( empty( $level ) || empty( $country ) ) {
		return $value;
	}

	$level_id = $level->id;

	if ( array_key_exists( $level_id, $restricted_countries ) ) {
		$country_list = $restricted_countries[ $level_id ];
		if ( in_array( $country, $country_list, true ) ) {
			$pmpro_msg  = 'Your country of residence is not permitted to register for this level.';
			$pmpro_msgt = 'pmpro_error';
			return false;
		}
	}

	return $value;
}
add_filter( 'pmpro_registration_checks', 'pmpro_registration_check_restricted_by_country' );

function pmpro_level_expiration_text_restricted_by_country( $text, $level ) {
	global $restricted_countries, $pmpro_countries;

	if ( array_key_exists( $level->id, $restricted_countries ) ) {
		$country_list = $restricted_countries[ $level->id ];

		$text .= ' ' . 'This level cannot be purchased if you reside in the following countries: ';

		$text .= implode( ', ', array_map( function( $country_code ) use ( $pmpro_countries ) {
			return isset( $pmpro_countries[ $country_code ] ) ? $pmpro_countries[ $country_code ] : $country_code;
		}, $country_list ) );
	}

	return $text;
}
add_filter( 'pmpro_level_expiration_text', 'pmpro_level_expiration_text_restricted_by_country', 10, 2 );