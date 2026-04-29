<?php
/**
 * Automatically approve users added by admin.
 *
 * title: Automatically approve users added by admin
 * layout: snippet
 * collection: add-ons
 * category: pmpro-approvals
 * link: https://www.paidmembershipspro.com/skip-approval-admin-added-members/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_automatically_approve_admin_added_user( $level_id, $user_id, $cancelled_level ) {

    // Bail if not an admin or if Approvals Add On is not active or level doesn't require approval.
	if ( ! ( current_user_can( 'manage_options' ) || ! class_exists( 'PMPro_Approvals' ) || ! PMPro_Approvals::requiresApproval( $level_id ) ) ) {
		return;
	}

	// Let's approve the user.
	PMPro_Approvals::approveMember( $user_id, $level_id );

}
add_action( 'pmpro_after_change_membership_level', 'my_pmpro_automatically_approve_admin_added_user', 10, 3 );
