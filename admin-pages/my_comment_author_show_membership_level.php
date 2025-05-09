<?php
/**
 * Show Membership Level in Comment Author Line
 *
 * This recipe appends the member's level name to their comment author label,
 * when a comment is left by a logged-in member.
 *
 * title: Show Membership Level in Comment Author Line
 * layout: snippet
 * collection: admin-pages
 * category: comments
 * link: https://www.paidmembershipspro.com/show-a-members-level-name-in-post-comments/
 *
 * Add this code to a custom plugin or use the Code Snippets plugin.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_comment_author_show_membership_level( $author_text, $comment_ID ) {
	$email = get_comment_author_email( $comment_ID );
	if( empty( $email ) ) {
		return $author_text;
	}

	$user = get_user_by( 'email', $email );
	if( empty( $user ) ) {
		return $author_text;
	}

	if( function_exists( 'pmpro_hasMembershipLevel' ) && pmpro_hasMembershipLevel( NULL, $user->ID ) ) {
		$level = pmpro_getMembershipLevelForUser( $user->ID );
		$author_text = $author_text . ' (' . $level->name . ')';
	}

	return $author_text;
}
add_filter( 'comment_author', 'my_comment_author_show_membership_level', 10, 2 );