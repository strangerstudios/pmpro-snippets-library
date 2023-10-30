<?php
/** 
 * Create your own member RSS feed
 * 
 * Extra feeds format:
 * $feeds["Label"] = Feed URL
 *
 * title: Create your own member RSS feed
 * layout: snippet
 * collection: add-ons, pmpro-member-rss
 * category: rss, rss-feed
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpromrss_feeds( $feeds ) {
    // Recent posts by current user.
    global $current_user;
    $feeds['My Recent Posts'] = pmpromrss_url( home_url( '/author/' . $current_user->user_login . '/feed/' ) );

    // Recent posts from "members" category.
    $feeds['Members Feed'] = pmpromrss_url( home_url( '/category/members/feed' ) );

    return $feeds;

}
add_action('pmpromrss_feeds', 'my_pmpromrss_feeds');