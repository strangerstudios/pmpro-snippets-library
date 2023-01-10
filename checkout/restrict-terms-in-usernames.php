<?php
/**
 * Exclude specific terms from usernames during membership checkout.
 * Edit line 44 to set your unique list of terms to block.
 *
 * title: Exclude specific terms from usernames during membership checkout.
 * layout: snippet
 * collection: checkout
 * category: registration-check
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
* Check if the username used at checkout contains an invalid term.
* Block checkout and show an error message if the username is invalid.
*
*/
function invalid_username_pmpro_registration_checks( $value ) {
	// Get username from the form submission.
	$username = $_REQUEST['username'];

	// Check username for invalid terms.
	if ( my_check_for_invalid_username_terms( $username ) ) {
		global $pmpro_msg, $pmpro_msgt;
		$pmpro_msg = "Hey, that's not a nice username!";
		$pmpro_msgt = "pmpro_error";
		$value = false;
	}

	// Return whether the check is good or bad.
	return $value;
}
add_filter( 'pmpro_registration_checks', 'invalid_username_pmpro_registration_checks', 10, 1 );

/**
 * Check the passed username for your custom defined invalid terms.
 */
function my_check_for_invalid_username_terms( $username ) {
	// Update this variable to include the terms you want to block, separated by bars.
	$invalid_terms = 'duck|pluck';

	// Check submitted username for any of the invalid terms.
	if ( preg_match( "/(" . $invalid_terms . ")/i", $username ) ) {
		return true;
	}

	// If the username is ok, return false.
	return false;
}
