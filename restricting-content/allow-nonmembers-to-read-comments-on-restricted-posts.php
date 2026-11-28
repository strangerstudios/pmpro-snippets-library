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
 * Non-members see the comments in the post's comment section and in comment blocks
 * like Latest Comments. The comment form stays closed, and comment feeds and the
 * REST API still hide these comments.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_show_restricted_post_comments_to_non_members() {
	remove_filter( 'comments_array', 'pmpro_comments_filter', 10, 2 );
	// PMPro 3.9.2+ also removes these comments from comment queries.
	remove_filter( 'the_comments', 'pmpro_comments_query_filter' );
}
add_action( 'init', 'my_pmpro_show_restricted_post_comments_to_non_members' );
