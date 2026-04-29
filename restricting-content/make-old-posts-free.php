<?php
/**
 * Make older posts available for free to all visitors.
 * This is useful for sites that want to release archived content publicly
 * after a certain amount of time has passed. Adjust the timeframe in the code
 * as needed (default: 18 months). (change line 32)
 *
 * title: title: Make Old Posts Free After a Set Time
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
 function my_pmpro_make_old_posts_free($hasaccess, $post, $user, $post_membership_levels) {
	// If they already have access, respect that and return.
	if ( $hasaccess ) {
		return $hasaccess;
	}

	// Only make posts (not pages or custom post types) free.
	if ( $post->post_type != 'post' ) {
		return $hasaccess;
	}

	// Check the publish date and unlock if older than 18 months.
	$published = strtotime( $post->post_date, current_time( 'timestamp' ) );
	if ( $published < strtotime( '-18 Months', current_time( 'timestamp' ) ) ) {
		$hasaccess = true;
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'my_pmpro_make_old_posts_free', 10, 4 );
