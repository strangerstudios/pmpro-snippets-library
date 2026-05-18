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
 * snippet redirects those requests to the home page for anyone without
 * the edit_posts capability — so admins, editors, and authors can still
 * view author archives while subscribers and logged-out visitors can't.
 *
 * If you want author enumeration available to all logged-in users (subscribers
 * included), swap ! current_user_can( 'edit_posts' ) for ! is_user_logged_in().
 */
function my_block_author_enumeration() {
	if ( ! current_user_can( 'edit_posts' ) && isset( $_GET['author'] ) ) {
		wp_safe_redirect( home_url() );
		exit;
	}
}
add_action( 'init', 'my_block_author_enumeration' );
