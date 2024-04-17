<?php
/**
 * Restrict group child checkout to email domain of group leader.
 * 
 * title: Restrict group child checkout to email domain of group leader
 * layout: snippet
 * collection: add-ons, pmpro-group-accounts
 * category: members, groups, email
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmprogroupacct_enforce_same_email_domain( $continue ) {
	global $pmpro_level, $discount_code, $current_user;

	// Let's only do this if checkout is OK and Group Accounts is active.
	if ( ! $continue || empty( $_REQUEST['pmprogroupacct_group_code'] ) || ! class_exists( 'PMProGroupAcct_Group' ) ) {
		return $continue;
	}

	// Get the group.
	$group = PMProGroupAcct_Group::get_group_by_checkout_code( sanitize_text_field( $_REQUEST['pmprogroupacct_group_code'] ) );
	if ( empty( $group ) ) {
		// Group not found. Another registration check should handle this case.
		return $continue;
	}

	// Get the parent user.
	$parent_user = get_userdata( $group->group_parent_user_id );
	if ( empty( $parent_user ) ) {
		// Parent user not found. Another registration check should handle this case.
		return $continue;
	}

	// Get the email domain of the parent user.
	$parent_email_domain = substr( strrchr( $parent_user->user_email, '@' ), 1 );

	// Get the email domain of the user.
	if ( ! $current_user->ID && ! empty( $_REQUEST['bemail'] ) ) {
		$user_email_domain = substr( strrchr( $_REQUEST['bemail'], '@' ), 1 );
	} elseif ( $current_user ) {
		$user_email_domain = substr( strrchr( $current_user->user_email, '@' ), 1 );
	}

	// If the email domain of the user is not the same as the email domain of the parent user, return false.
	if ( $parent_email_domain !== $user_email_domain ) {
		pmpro_setMessage( __( 'Please use a valid email domain for your group account.', 'pmpro-group-accounts' ), 'pmpro_error' );
		return false;
	}

	return $continue;
}
add_filter( 'pmpro_registration_checks', 'my_pmprogroupacct_enforce_same_email_domain', 15, 1 ); // set priority to 15 or later.