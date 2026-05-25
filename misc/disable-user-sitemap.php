<?php
/**
 * Disable the WordPress user sitemap so usernames aren't exposed.
 *
 * title: Disable the user sitemap to hide usernames
 * layout: snippet
 * collection: misc
 * category: security
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/*
 * Unregister the entire 'users' sitemap provider, so no user sitemap pages
 * are generated. Other providers (posts, pages, taxonomies) are unaffected.
 */
function my_disable_user_sitemap( $provider, $name ) {
	if ( 'users' === $name ) {
		return false;
	}
	return $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'my_disable_user_sitemap', 10, 2 );
