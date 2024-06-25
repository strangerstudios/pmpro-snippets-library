<?php
/**
 * Require user fields when a member updates their profile on the Member Profile Edit page.
 *
 * This code hooks into the form submission to require specific fields on the $user before saving
 * and adds to the error message shown on submit failure. We do not require user fields by default
 * on this frontend page so that other profile field changes aren't missed or lost on save.
 * 
 * title: Require user fields on the Member Profile Edit frontend page.
 * collection: user-fields
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function pmpro_make_profile_fields_required( &$errors, $update = null, &$user = null ) {
	// Validate first name.
	if ( empty( $user->first_name ) ) {
		$errors[] = 'Please enter a first name.';
	}
}
add_action( 'pmpro_user_profile_update_errors', 'pmpro_make_profile_fields_required', 10, 3 );
