<?php
/**
 * Add custom user fields to the email admins receive when approving the member.
 * Create User Fields inside Paid Memberships Pro first, then update/duplicate
 * line 28 with your field names.
 *
 * title: Add custom user fields to Approvals emails for admins.
 * layout: snippet
 * collection: add-ons
 * category: pmpro-approvals
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Adds custom user fields to the email admins receive when approving the member.
 *
 * @param array   $data   Email template variables.
 * @param WP_User $member The user whose status was changed.
 * @param WP_User $admin  The admin user who the email is being sent to.
 *
 * @return array
 */
function my_pmpro_add_user_fields_to_admin_approval_emails( $data, $member, $admin ) {
    $data['company'] = $member->company; // Update/duplicate this line for your user field names.
    return $data;
}
add_filter( 'pmpro_approvals_admin_approved_email_data', 'my_pmpro_add_user_fields_to_admin_approval_emails', 10, 3 );
add_filter( 'pmpro_approvals_admin_pending_email_data', 'my_pmpro_add_user_fields_to_admin_approval_emails', 10, 3 );
add_filter( 'pmpro_approvals_admin_denied_email_data', 'my_pmpro_add_user_fields_to_admin_approval_emails', 10, 3 );
