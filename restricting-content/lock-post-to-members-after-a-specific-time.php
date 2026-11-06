<?php
/**
 * Lock posts to members-only after a set timeframe.
 * New posts that require membership stay public for a limited time, then
 * require a membership level to view. Update line 25 to your timeframe.
 *
 * title: Lock Posts to Members-Only After a Set Timeframe
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction
 * link: https://www.paidmembershipspro.com/lock-posts-members-specific-timeframe/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_lock_posts_after_timeframe( $hasaccess, $thepost, $theuser, $post_membership_levels ) {
    // If PMPro already allows access, respect that and return.
    if ( $hasaccess ) {
        return $hasaccess;
    }

    // Determine the cutoff date (24 hours ago).
    $cutoff    = strtotime( '-24 Hours', current_time( 'timestamp' ) );
    $published = strtotime( $thepost->post_date, current_time( 'timestamp' ) );

    // If the post was published after the cutoff date, allow access.
    if ( $published >= $cutoff ) {
        $hasaccess = true;
    }

    return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'my_pmpro_lock_posts_after_timeframe', 10, 4 );