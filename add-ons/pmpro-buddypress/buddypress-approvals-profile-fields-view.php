<?php
/**
 * Hides multiple buddypress profile fields when an unapproved member views someone else's profile.
 * Allows all members to see their own profiles without restrictions.
 * Logs debugging info so you can check if the function is running correctly.
 * Change 41 to the names of your BuddyPress Profile fields, Change line 45 to the message
 * When using the Approvals Add On from PMPro 
 * 
 * title: Hide BuddyPress Profile Fields marked "members only", on the BuddyPress Profile Tab
 * layout: snippet-example
 * collection: pmpro-buddypress, pmpro_approvals
 * category: buddypress, buddyboss
 * link: none
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_pmpro_hide_bp_fields( $field_value ) {
    if ( ! is_user_logged_in() || bp_displayed_user_id() == get_current_user_id() ) {
        return $field_value; // Allow logged-out users and users viewing their own profile
    }
    
    if( ! function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
     return $field_value;
    }

    if ( ! class_exists( 'PMPro_Approvals' ) || ! pmpro_hasMembershipLevel( null, get_current_user_id() ) ) {
        return $field_value; // Exit if PMPro Approvals is not active or no membership
    }

    $level = pmpro_getMembershipLevelForUser( get_current_user_id() );
    if ( ! $level || ! PMPro_Approvals::requiresApproval( $level->id ) ) {
        return $field_value; // Exit if level does not require approval
    }

    $approval_status = get_user_meta( get_current_user_id(), 'pmpro_approval_' . $level->id, true )['status'] ?? '';

    // Define restricted fields
    $restricted_fields = ['Email', 'Phone Number', 'Address', 'Company'];

    // Hide field if the viewer is not approved
    if ( $approval_status !== 'approved' && in_array( bp_get_the_profile_field_name(), $restricted_fields, true ) ) {
        return __( 'Restricted to Approved Members', 'paid-memberships-pro' );
    }

    return $field_value;
}
add_filter( 'bp_get_the_profile_field_value', 'my_pmpro_hide_bp_fields' );