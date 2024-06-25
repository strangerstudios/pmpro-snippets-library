<?php
/**
 * Require a membership level to view comments on any post.
 *
 * title: Require a membership level to view comments on any post.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, admin-access, comments
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
/**
 * Makes all comments on posts, including open/public posts to require a membership level to view these.
 * PMPro by default restricts comments in restricted posts to the relevant membership level. 
 */
function my_pmpro_hide_comments_on_open_posts( $comments ) {
	// Restrict comments to only logged-in members that have any active membership level.
	if ( function_exists( 'pmpro_hasMembershipLevel' ) && ! pmpro_hasMembershipLevel() ) {
		$comments = array();
		add_action( 'comment_form_before', 'pmpro_non_member_text_before_comment_form' );
	} 

	return $comments;
	
}
add_filter( 'comments_array', 'my_pmpro_hide_comments_on_open_posts' );

/**
 * Helper function to display the message that the comments are for members only.
 * Programatically loads the message from the above function.
 * 
 * Filters the comments wording for non-members to be more appropriate.
 *
 * @return string $pmpro_comments_non_member_text The message to display to non-members when viewing the comment section.
 */
function pmpro_non_member_text_before_comment_form() {
	$pmpro_comments_non_member_text = pmpro_get_no_access_message( null, null, null );
	$pmpro_comments_non_member_text = str_replace( __( 'This content is', 'pmpro-comments' ), __( 'Comments are', 'pmpro-comments' ), $pmpro_comments_non_member_text );

	echo wp_kses_post( $pmpro_comments_non_member_text );
}
