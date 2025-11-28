<?php
/**
 * Redirect the Membership Account page to the bbPress User Profile.
 *
 * title: Redirect Membership Account to bbPress User Profile
 * layout: snippet
 * collection: bbpress
 * category: redirect, bbpress
 * link: https://www.paidmembershipspro.com/set-bbpress-user-profile-membership-account-page/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_bbpress_profile_template_redirect() {
	global $pmpro_pages, $current_user;

	// Make sure PMPro is active.
	if ( empty( $pmpro_pages ) ) {
		return;
	}

	// Make sure bbPress is active.
	if ( ! function_exists( 'bbp_get_user_profile_url' ) ) {
		return;
	}

	// Redirect the Membership Account page to the bbPress User Profile.
	if ( is_page( $pmpro_pages['account'] ) ) {
		wp_redirect( bbp_get_user_profile_url( bbp_get_current_user_id() ) );
		exit;
	}
}
add_action( 'template_redirect', 'my_pmpro_bbpress_profile_template_redirect' );
