<?php
/**
 * Allow non-members to read comments on restricted posts.
 *
 * title: Display Comments on a Members-Only Post to Non-Members
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, comments
 * link: https://www.paidmembershipspro.com/display-comments-of-a-member-only-blog-post-to-non-members/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_show_restricted_post_comments_to_non_members() {
	remove_filter( 'comments_array', 'pmpro_comments_filter', 10, 2 );
}
add_action( 'init', 'my_pmpro_show_restricted_post_comments_to_non_members' );
