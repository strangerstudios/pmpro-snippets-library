<?php
/**
 * Mark a member "paused" and deny access to members-only content.
 * Show a message that their account is paused containing a link to your website contact page.
 *
 * title: Mark a member "paused" and deny access to members-only content
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, member-access
 *
 * link:  https://www.paidmembershipspro.com/block-pause-members-access-restricted-content/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmprouf_init_pause_access_new() {
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return false;
	}

	// Bail if not in administrative area.
	if ( ! is_admin() ) {
		return;
	}

	pmpro_add_field_group( 'Access to Member Content' );

	// Define the field.
	$field = new PMPro_Field(
		'pmpro_paused_user',
		'checkbox',
		array(
			'label'   => 'Pause User',
			'text'    => 'Deny Access to Member Content',
			'profile' => 'admins',
		)
	);

	pmpro_add_user_field( 'Access to Member Content', $field );
}
add_action( 'init', 'my_pmprouf_init_pause_access_new', 10 );

/**
 * Deny access to member content if user is 'paused'.
 */
function paused_member_pmpro_has_membership_access_filter( $access, $post, $user, $levels ) {
	// If no access already, or no user/levels, leave access unchanged.
	if ( ! $access || empty( $user ) || empty( $user->ID ) || empty( $levels ) ) {
		return $access;
	}

	// Check if user is paused.
	$paused_user = get_user_meta( $user->ID, 'pmpro_paused_user', true );
	if ( ! empty( $paused_user ) ) {
		$access = false;
	}

	return $access;
}
add_filter( 'pmpro_has_membership_access_filter', 'paused_member_pmpro_has_membership_access_filter', 10, 4 );

/**
 * Show a different no-access message for users with paused membership.
 */
function paused_member_pmpro_no_access_message_body( $body, $level_ids ) {
	global $current_user;

	// Check if the current user is paused.
	$paused_user = get_user_meta( $current_user->ID, 'pmpro_paused_user', true );
	if ( ! empty( $paused_user ) ) {
		$body = __( '<p>Your membership is paused. Please contact us to reinstate your membership.</p><a href="/contact/">Contact Us</a>', 'paid-memberships-pro' );
	}

	return $body;
}
add_filter( 'pmpro_no_access_message_body', 'paused_member_pmpro_no_access_message_body', 10, 2 );

/**
 * Show a message on the Membership Account page for paused members.
 */
function paused_member_pmpro_membership_account_filter( $content ) {
	global $pmpro_pages, $current_user;

	if ( is_user_logged_in() && is_page( $pmpro_pages['account'] ) ) {
		$paused_user = get_user_meta( $current_user->ID, 'pmpro_paused_user', true );
		if ( ! empty( $paused_user ) ) {
			// Message to show on the account page.
			$new_content = __( '<div class="pmpro_content_message"><p>Your membership is paused. Please contact us to reinstate your membership.</p><a href="/contact/">Contact Us</a></div>', 'paid-memberships-pro' );
			$content = $new_content . $content;
		}
	}

	return $content;
}
add_filter( 'the_content', 'paused_member_pmpro_membership_account_filter', 10 );
