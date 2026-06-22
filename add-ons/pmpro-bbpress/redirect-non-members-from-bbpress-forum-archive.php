<?php
/**
 * Redirect non-members from the bbPress forums and topics archive pages.
 *
 * title: Redirect Non-Members from the bbPress Forums and Topics Archive Pages
 * layout: snippet
 * collection: bbpress
 * category: redirect, bbpress
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_bbpress_archives_redirect_non_members() {
	// Let's only do this if bbPress & PMPro is active.
	if ( ! function_exists( 'bbp_is_forum_archive' ) || ! function_exists( 'bbp_is_topic_archive' ) || ! function_exists( 'pmpro_hasMembershipLevel' ) ) {
		return;
	}

	// Let's check if we're on the bbPress forums or topics archive page.
	if ( ( bbp_is_forum_archive() || bbp_is_topic_archive() ) && ! pmpro_hasMembershipLevel() ) {
		// Redirect the user to the membership levels page.
		wp_safe_redirect( pmpro_url( 'levels' ) );
		exit;
	}
}
add_action( 'template_redirect', 'my_pmpro_bbpress_archives_redirect_non_members' );
