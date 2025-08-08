<?php
/**
 * Do not return the post thumbnail (featured image) on restricted content 
 * when viewed by a non-member.
 *
 * title: Hide a Post’s Featured Image From Non-Members
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 * link: https://www.paidmembershipspro.com/hide-a-blog-posts-featured-image-from-non-members/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function hide_post_thumbnail_on_restricted_content( $html, $post_id, $post_image_id ) {
	if ( function_exists( 'pmpro_has_membership_access' ) ) {

		// Check if the user has access to the post.
		$hasaccess = pmpro_has_membership_access( $post_id );

		if ( empty( $hasaccess ) ) {
			$html = '';
		}
	}
	return $html;
}
add_filter( 'post_thumbnail_html', 'hide_post_thumbnail_on_restricted_content', 10, 3 );
