<?php
/**
 * Prevent PMPro LPV from granting free acess to a specific set of posts.
 *
 * title: Prevent the Limit Post Views Add On From Granting Free Access to Specific Posts
 * layout: snippet
 * category: limit post views, banner
 * link: https://www.paidmembershipspro.com/override-limit-post-views/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Prevent PMPro LPV from granting free acess to a specific set of posts.
 *
 * @param bool $allow_free_views True if the user should be allowed to view the post for free.
 * @param WP_Post $post The post object.
 * @return bool
 */
function my_pmprolpv_always_restrict_posts( $allow_free_views, $post ) {
    // Add the post IDs that should always require a membership to view.
    $restricted_post_ids = array( 100, 101, 102 );

    // If the post is in the restricted list, return false to prevent free access.
    if ( in_array( $post->ID, $restricted_post_ids ) ) {
        return false;
    }

    // Otherwise, return the original value.
    return $allow_free_views;
}
add_filter( 'pmprolpv_has_membership_access', 'my_pmprolpv_always_restrict_posts', 10, 2 );
