<?php 
/**
 * Redirect all members and non-members on logout to the homepage or a specified page on your site
 *
 * title: Redirect all users after logout.
 * layout: snippet
 * collection: misc
 * category: logout, redirect
 * link: https://www.paidmembershipspro.com/redirect-logout/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_redirect_after_logout_wp() {
	// Redirect to site home page.
	wp_safe_redirect( home_url() );

	// Uncomment the line below and comment out line 18 above to redirect to a specified page 
	// wp_safe_redirect( home_url( 'your-page-slug-here' ) ); // replace your-page-slug-here with the slug of your specific page

	exit;
}
add_action( 'wp_logout', 'my_redirect_after_logout_wp' );
