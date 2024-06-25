<?php
/**
 * Filter to only allow access to a protected post by post ID after a specific calendar date.
 * Learn more at: https://www.paidmembershipspro.com/unlock-content-specific-dates/
 *
 * Update the $posts_in_series_with_dates array with your post IDs => date (formatted as YYYY-MM-DD).
 *
 * title: Unlock post IDs on specific dates.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_has_membership_access_on_date( $hasaccess, $post, $user ) {
	// If they already don't have access, return.
	if ( ! $hasaccess ) {
		return $hasaccess;
	}

	// Set the post IDs and dates in an array.
	$posts_in_series_with_dates = array(
		'123' => '2023-10-28',
		'456' => '2023-10-29',
		'789' => '2023-10-30'
	);

	// Check if the post is in the array.
	if ( array_key_exists( $post->ID, $posts_in_series_with_dates ) ) {
		// Check if the current time of the WordPress site server is less than the date in the array.
		if ( current_time( 'timestamp' ) < strtotime( $posts_in_series_with_dates[$post->ID], current_time( 'timestamp' ) ) ) {
			$hasaccess = false;
		}
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'my_pmpro_has_membership_access_on_date', 10, 3 );
