<?php
/**
 * Block author enumeration via ?author=N requests.
 *
 * title: Block author enumeration to hide usernames
 * layout: snippet
 * collection: misc
 * category: security, login
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * By default, WordPress will resolve a URL like /?author=1 to the author's
 * archive, which exposes the user's login slug in the redirect URL. This
 * snippet redirects those requests to the home page for logged-out users
 * before WordPress resolves the slug.
 */
function my_block_author_enumeration() {
	if ( ! is_user_logged_in() && isset( $_GET['author'] ) ) {
		wp_safe_redirect( home_url() );
		exit;
	}
}
add_action( 'init', 'my_block_author_enumeration' );
