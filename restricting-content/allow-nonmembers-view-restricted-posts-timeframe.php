<?php
/**
 * Allow non-members to view restricted posts if they are less than 30 days old.
 * This is helpful if you want to make new content freely available for a limited time,
 * then require a membership to access after that timeframe.
 *
 * title: Allow Non-Members to View Restricted Posts Based on Timeframe
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction
 * link: https://www.paidmembershipspro.com/lock-unlock-posts-based-age-post-date/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
// change line 26 to the timeframe you want to allow access.

function open_new_posts_to_non_members( $hasaccess, $thepost, $theuser, $post_membership_levels ) {
	// If PMPro already allows access, respect that and return.
	if ( $hasaccess ) {
		return $hasaccess;
	}

	// Determine the cutoff date (30 days ago).
	$cutoff    = strtotime( '-30 Days', current_time( 'timestamp' ) );
	$published = strtotime( $thepost->post_date, current_time( 'timestamp' ) );

	// If the post was published after the cutoff date, allow access.
	if ( $published > $cutoff ) {
		$hasaccess = true;
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'open_new_posts_to_non_members', 10, 4 );