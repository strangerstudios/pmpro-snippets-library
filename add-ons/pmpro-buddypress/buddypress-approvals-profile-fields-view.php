<?php
/**
 * Hides multiple buddypress profile fields when an unapproved member views someone else's profile or is logged-out.
 * Allows all members to see their own profiles without restrictions.
 * Adjust the $restricted_fields array to include the names of the fields you want to restrict and tweak this code recipe further for your needs.
 * 
 * title: Hide BuddyPress Profile Fields marked "members only", on the BuddyPress Profile Tab
 * layout: snippet-example
 * collection: pmpro-buddypress, pmpro_approvals
 * category: buddypress, buddyboss
 * link: TBD
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_hide_bp_fields( $field_value ) {
	global $current_user;
	
	// Make sure PMPro is installed and available, if not bail.
    if ( ! function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
        return $field_value;
    }
	
	// Allow users viewing their own profile regardlress of status.
    if ( bp_displayed_user_id() == $current_user->ID ) {
        return $field_value;
    }
	
	// Define restricted fields we want to lockdown.
	$restricted_fields = array( 'Email', 'Phone Number', 'Address', 'Company' );

	// Check if the field being displayed is a restricted field.
	if ( ! in_array( bp_get_the_profile_field_name(), $restricted_fields, true ) ) {
		return $field_value;
	}

	// If a user is logged-out we can also hide this information.
	if ( ! is_user_logged_in() ) {
		return 'Please login to view content.';
	}

		// Let's not fatal error if PMPro Approvals is deactivated.
	if ( ! class_exists( 'PMPro_Approvals' ) ) {
		return $field_value;
	}

		// See if the current user is not approved, then lockdown the content.
	$level = pmpro_getMembershipLevelForUser( $current_user->ID );
	$approved = PMPro_Approvals::isApproved( $current_user->ID, $level->id );

	// Hide field if the viewer is not approved
	if ( ! $approved ) {
		return __( 'Restricted to Approved Members', 'paid-memberships-pro' );
	}

	return $field_value;
}
add_filter( 'bp_get_the_profile_field_value', 'my_pmpro_hide_bp_fields' );