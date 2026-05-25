<?php
/**
 * Redirect non-admin users to a Coming Soon page.
 *
 * title: Redirect non-admin users to a Coming Soon page
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, redirects
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_coming_soon_redirect() {
	// Set this to your Coming Soon page's ID.
	$coming_soon_page_id = 123;

	// Bail if the Coming Soon page doesn't exist or isn't published.
	// This prevents a redirect loop and keeps the site accessible if the ID is wrong.
	if ( 'publish' !== get_post_status( $coming_soon_page_id ) ) {
		return;
	}

	// If we're already on the Coming Soon page, do nothing.
	if ( is_page( $coming_soon_page_id ) ) {
		return;
	}

	// Let admins through (they're building the site).
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}

	// Allow the login and admin pages so admins can log in.
	// Match the path prefix exactly so a URL like /wp-administrators/ doesn't slip through.
	if ( strpos( $_SERVER['REQUEST_URI'], '/wp-admin/' ) === 0
		|| strpos( $_SERVER['REQUEST_URI'], '/wp-login.php' ) === 0 ) {
		return;
	}

	// Everyone else gets the Coming Soon page.
	wp_safe_redirect( get_permalink( $coming_soon_page_id ) );
	exit;
}
add_action( 'template_redirect', 'my_coming_soon_redirect' );
