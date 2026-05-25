<?php
/**
 * Require Approval for Every Signup
 * 
 * This recipe forces every checkout to require approval.
 *
 * Requires the Approvals Add On to be active for this to work.
 *
 * title: Require Approval for Every Signup
 * layout: snippet
 * collection: add-ons, pmpro-approvals
 * category: approvals
 * link: TBD
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_require_approval_for_every_signup( $user_id, $morder ) {

	if ( ! class_exists( 'PMPro_Approvals' ) ) {
		return;
	}

	$level_id = $morder->membership_level->id;

	if ( ! PMPro_Approvals::requiresApproval( $level_id ) ) {
		return;
	}

	update_user_meta(
		$user_id,
		'pmpro_approval_' . $level_id,
		array(
			'status'    => 'pending',
			'timestamp' => time(),
			'who'       => '',
			'approver'  => '',
		)
	);

	// Delete the approval count cache.
	delete_transient( 'pmpro_approvals_approval_count' );
}
add_action( 'pmpro_after_checkout', 'my_pmpro_require_approval_for_every_signup', 99, 2 );