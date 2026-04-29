<?php
/**
 * Show a custom "available on date" message to members who have the required level,
 * while still showing default Protected Content messages to non-members.
 *
 * Customize for your site: Update the $posts_in_series_with_dates array with your post IDs => date (formatted as YYYY-MM-DD).
 *
 * title: Unlock post IDs on specific dates (Members Only Message)
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction
 * link: https://www.paidmembershipspro.com/unlock-content-specific-dates/
 */

function my_pmpro_show_unlock_date_for_members( $content ) {
	if ( ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	global $post, $current_user;

	// Define post IDs and their unlock dates.
	$posts_in_series_with_dates = array(
		'830' => '2027-10-01',
		'834' => '2027-10-15',
		'902' => '2027-10-30'
	);

	// If this post is not in the list, just return the original content.
	if ( ! array_key_exists( $post->ID, $posts_in_series_with_dates ) ) {
		return $content;
	}

	// Check membership access first.
	if ( ! pmpro_has_membership_access( $post->ID, $current_user->ID ) ) {
		// Non-members see default PMPro protected message (no override).
		return $content;
	}

	// Member has access level, now check date restriction.
	$unlock_timestamp  = strtotime( $posts_in_series_with_dates[ $post->ID ] );
	$current_timestamp = current_time( 'timestamp' );

	if ( $current_timestamp < $unlock_timestamp ) {
		$formatted_date = date_i18n( get_option( 'date_format' ), $unlock_timestamp );
		$message = '<div class="pmpro-content-note">This content will be available with your membership on <strong>' . esc_html( $formatted_date ) . '</strong>.</div>';

		// Replace full content on single view, or excerpt on archives.
		return $message;
	}

	return $content;
}
add_filter( 'the_content', 'my_pmpro_show_unlock_date_for_members' );
add_filter( 'the_excerpt', 'my_pmpro_show_unlock_date_for_members' );