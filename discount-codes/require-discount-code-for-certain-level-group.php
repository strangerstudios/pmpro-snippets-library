<?php
/**
 * Require a discount code to checkout for any level in a specific level group.
 *
 * When a member attempts to check out for any membership level that belongs to
 * the specified level group, they must enter a discount code to proceed. If no
 * code is entered, checkout is blocked with an error message.
 *
 * title: Require a Discount Code to Checkout for Any Level in a Level Group
 * layout: snippet
 * collection: checkout
 * category: discount-codes
 * link: https://www.paidmembershipspro.com/require-a-discount-code-to-checkout-for-a-certain-level/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_registration_checks_require_code_to_register_for_group( $pmpro_continue_registration ) {
	// Only bother if things are okay so far.
	if ( ! $pmpro_continue_registration ) {
		return $pmpro_continue_registration;
	}

	global $pmpro_level, $discount_code;

	// Set the level group ID that requires a discount code.
	$required_group_id = 2; // Replace with your level group ID.

	// Return early if no level is being checked out.
	if ( empty( $pmpro_level ) ) {
		return $pmpro_continue_registration;
	}

	// Check if the level being purchased is in the required group.
	$group_id = pmpro_get_group_id_for_level( $pmpro_level->id );
	if ( empty( $group_id ) || $group_id != $required_group_id ) {
		return $pmpro_continue_registration;
	}

	// A discount code is required for levels in this group.
	if ( empty( $discount_code )  ) {
		pmpro_setMessage( 'You must use a valid discount code to register for this level.', 'pmpro_error' );
		return false;
	}

	return $pmpro_continue_registration;
}
add_filter( 'pmpro_registration_checks', 'my_pmpro_registration_checks_require_code_to_register_for_group' );
