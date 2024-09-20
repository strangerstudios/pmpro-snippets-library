<?php
/**
 * Redirect the empty category or tag archive to levels page when all posts are hidden from searches and archives.
 * Learn more at https://www.paidmembershipspro.com/redirect-empty-category-or-tag-archive-when-members-only-content/
 *
 * title: Redirect the empty category or tag archive to levels page when posts are hidden.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function pmpro_category_no_results_redirect() {
	if ( is_category() || is_tax() ) {
		global $wp_query;
        	if ( $wp_query->found_posts === 0 ) {
			global $pmpro_pages; // Assuming $pmpro_pages is a global variable
			$redirect_url = get_permalink($pmpro_pages['levels']);
			if ( $redirect_url ) {
				wp_redirect( $redirect_url );
				exit;
			}
		}
	}
}
add_action( 'template_redirect', 'pmpro_category_no_results_redirect' );
