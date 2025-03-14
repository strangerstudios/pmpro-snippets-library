<?php
/**
 * Email the member once per day (max) when their user pages are updated.
 *
 * title: Email Member When User Page is Updated
 * layout: snippet
 * collection: add-ons, pmpro-userpages
 * category: user pages
 * link: https://www.paidmembershipspro.com/user-pages-user-page-only/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_pmpro_user_pages_updated_send_email( $post_id ) {
	// Bail if User Pages is not enabled
	if ( ! function_exists( 'pmproup_get_page_for_user' ) ) {
		return;
	}

	// Basic validation checks
	if ( wp_is_post_revision( $post_id ) || ! is_admin() ||
			! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-post_' . $post_id ) ) {
		return;
	}

	// Get the updated post
	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	// Use get_user_meta to retrieve the user page for the post author
	$user_page = get_user_meta( $post->post_author, 'pmproup_user_page', true );
	if ( empty( $user_page ) ) {
		return;
	}

	// Validate that the updated post is either the user page itself or a child of it.
	if ( intval( $post->ID ) !== intval( $user_page ) && intval( $post->post_parent ) !== intval( $user_page ) ) {
		return;
	}

	// Check if we've emailed recently and get author email
	$author_email = get_the_author_meta( 'user_email', $post->post_author );
	if ( ! is_email( $author_email ) || 
			strtotime( get_user_meta( $post->post_author, 'pmproup_user_page_updated_emailed', true ) ) > strtotime( '-1 day' ) ) {
		return;
	}

	// Prepare email content
	$site_name = get_bloginfo( 'name' );
	$subject = sprintf( '[%s] Your User Page "%s" Has Been Updated', $site_name, get_the_title( $post_id ) );
	$message = sprintf(
		"Hello,\n\n" .
		"Your user page \"%s\" has been updated on %s.\n\n" .
		"You can view your updated page here: %s\n\n" .
		"Best regards,\n" .
		"The %s Team",
		get_the_title( $post_id ),
		$site_name,
		get_permalink( $post_id ),
		$site_name
	);

	// Send email with HTML headers
	$sent = wp_mail( $author_email, $subject, nl2br( $message ), array( 'Content-Type: text/html; charset=UTF-8' ) );

	if ( $sent ) {
		update_user_meta( $post->post_author, 'pmproup_user_page_updated_emailed', current_time( 'mysql' ) );
	} else {
		error_log( sprintf( 'Failed to send user page update email to %s for post ID %d', $author_email, $post_id ) );
	}
}
add_action( 'save_post', 'my_pmpro_user_pages_updated_send_email' );
