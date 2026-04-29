<?php
/**
 * Restrict PMPro Membership Checkout by email domains.
 * Optionally set specific level IDs in the $restricted_levels
 * array to only restrict email domains for specified membership levels.
 *
 * To restrict all levels, leave the $restricted_levels array empty.
 * To restrict specific levels only, add the level IDs to the $restricted_levels array.
 * Make sure to edit the $valid_domains array defined in the my_check_for_valid_domain function
 * to include only the domains you would prefer to allow.
 *
 * title: Restrict PMPro Membership Checkout by email domains
 * layout: snippet
 * collection: checkout
 * category: registration-check
 * link: https://www.paidmembershipspro.com/restrict-membership-signup-by-email-domain/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_registration_checks_restrict_email_domains( $continue_registration ) {

	// Set the restricted levels array. Leave empty to apply to all levels.
	$restricted_levels = array();

	/**
	 * To apply email domain validation only for specific levels,
	 * uncomment the line below and set the IDs of the levels you want to restrict.
	 */
	// $restricted_levels = array( 2, 3, 4 ); // Set the level IDs you want to restrict.

	$level    = pmpro_getLevelAtCheckout(); // Get the level object.
	$level_id = intval( $level->id ); // Get the level ID.

	// Only check specific levels if $restricted_levels is not empty,
	// otherwise check all levels (skip this condition).
	if ( ! empty( $restricted_levels ) && ! in_array( $level_id, $restricted_levels ) ) {
		return $continue_registration; // Bail if the level is not restricted.
	}

	// Do we have an email?
	if ( ! isset( $_REQUEST['bemail'] ) || empty( $_REQUEST['bemail'] ) ) {
		return $continue_registration; // Bail if there is no email.
	}

	$email = wp_unslash( $_REQUEST['bemail'] ); // Get the email.

	if ( ! my_check_for_valid_domain( $email ) ) {
		pmpro_setMessage( 'Please enter a valid email address', 'pmpro_error' );
		return false;
	}

	return $continue_registration;
}
add_filter( 'pmpro_registration_checks', 'my_pmpro_registration_checks_restrict_email_domains', 10, 1 );

// Taken from: http://www.bitrepository.com/how-to-extract-domain-name-from-an-e-mail-address-string.html.
function my_get_domain_from_email( $email ) {
	// Get the data after the @ sign
	$domain = substr( strrchr( $email, '@' ), 1 );
	return $domain;
}

function my_check_for_valid_domain( $email ) {
	$domain = my_get_domain_from_email( $email );

	// Update this array to include the domains you want to allow.
	$valid_domains = array( 'yahoo.com', '*.gmail.com', '*.domain.uk' );

	foreach ( $valid_domains as $valid_domain ) {
		$components      = explode( '.', $valid_domain );
		$domain_to_check = explode( '.', $domain );

		if ( $components[0] == '*' && count( $domain_to_check ) > 2 ) {
			if ( $components[1] == $domain_to_check[1] && $components[2] == $domain_to_check[2] ) {
				return true;
			}
		} else {
			if ( ! ( strpos( $valid_domain, $domain ) === false ) ) {
				return true;
			}
		}
	}

	return false;
}