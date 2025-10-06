<?php
/**
 * Allow non-members to view specific restricted posts based on post age.
 * This recipe unlocks access to certain posts for a set period after they are published.
 * Once the timeframe expires, the content again requires a valid membership to view.
 *
 * title: Allow Non-Members to View Specific Restricted Posts Based on Post Age
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
function my_pmpro_open_specified_new_posts_per_date_to_non_members( $hasaccess, $thepost, $theuser, $post_membership_levels ) {

	// Set your post IDs and unlock periods here.
	$posts_to_unlock = array(
		845 => '-30 Days', // post ID 845, less than 30 days old
		451 => '-45 Days', // post ID 451, less than 45 days old
		155 => '-14 Days', // post ID 155, less than 14 days old
	);

	// If PMPro already grants access, respect that.
	if ( $hasaccess ) {
		return $hasaccess;
	}

	$thepost_id = $thepost->ID;

	// Only proceed if this post ID is one of the specified ones.
	if ( ! array_key_exists( $thepost_id, $posts_to_unlock ) ) {
		return $hasaccess;
	}

	// Calculate cutoff and published dates.
	$cutoff    = strtotime( $posts_to_unlock[ $thepost_id ], current_time( 'timestamp' ) );
	$published = strtotime( $thepost->post_date, current_time( 'timestamp' ) );

	// If the post is newer than the cutoff, grant access.
	if ( $published > $cutoff ) {
		$hasaccess = true;
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'my_pmpro_open_specified_new_posts_per_date_to_non_members', 10, 4 );