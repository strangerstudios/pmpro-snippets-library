<?php
/**
 * Allow non-members to view specific restricted posts based on post age.
 * This is helpful if you want to make some new content freely available for a limited time,
 * then require a membership to access after that timeframe.
 *
 * title: Allow Non-Members to View Specific Restricted Posts Based on Timeframe
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
function my_pmpro_open_specified_new_posts_to_non_members( $hasaccess, $thepost, $theuser, $post_membership_levels ) {

	// Set your post id's to unlock here
	$posts_to_unlock = array( 2, 35, 155 );  // array elements must be integers and not strings

	global $wpdb;

	//if PMPro says true already, return true
	if ( $hasaccess ) {
		return $hasaccess;
	}

	//figure out dates to check
	$thepost_id = $thepost->ID;
	$cutoff     = strtotime( '-30 Days', current_time( 'timestamp' ) );
	$published  = strtotime( $thepost->post_date, current_time( 'timestamp' ) );

	//if published after the cuttoff, then allow access for now
	if ( $published > $cutoff && in_array( $thepost_id, $posts_to_unlock, true ) ) {
		$hasaccess = true;
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'my_pmpro_open_specified_new_posts_to_non_members', 10, 4 );
