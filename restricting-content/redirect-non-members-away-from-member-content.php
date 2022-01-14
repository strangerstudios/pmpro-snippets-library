<?php
/**
 * Redirects members-only content to the Membership Levels page if a user is logged out or not a member.
 *
 * title: Redirect non-members away from member content.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, redirect
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_template_redirect_require_membership_access() {
	if ( function_exists( 'pmpro_has_membership_access' ) && ! pmpro_has_membership_access() ) {
		wp_redirect( pmpro_url( 'levels' ) );
		exit;
	}
}
add_action( 'template_redirect', 'my_template_redirect_require_membership_access' );
