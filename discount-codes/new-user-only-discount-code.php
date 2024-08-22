<?php
/**
 * Allow only new members to use a specific discount code(s).
 *
 * title: Create a Discount Code for New Members Only
 * layout: snippet
 * collection: discount-codes
 * category: discount-codes,new-members
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function new_members_only_check_discount_code( $okay, $dbcode, $level_id, $code ) {
	global $current_user;

	// Set discount codes that are for new members only.
	$discount_codes_for_new_members = array( 'newcode', 'anothercode' ); // Add more discount codes here

	// Make sure all values are lower case.
	$discount_codes_for_new_members = array_map( 'strtolower', $discount_codes_for_new_members );

	// Bail if the discount code is not one we are looking for.
	if ( ! $okay || ! in_array( strtolower( $code ), $discount_codes_for_new_members ) ) {
		return $okay;
	}

	// Get all membership levels, including inactive ones, for the current user.
	$user_levels = pmpro_getMembershipLevelsForUser( $current_user->ID, true );

	// Show an error message if the user has/had any membership levels.
	if ( $okay && ! empty( $user_levels ) ) {
		return 'The discount code entered is for new members only.';
	}

	return $okay;
}
add_filter( 'pmpro_check_discount_code', 'new_members_only_check_discount_code', 10, 4 );