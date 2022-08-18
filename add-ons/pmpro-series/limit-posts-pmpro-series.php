<?php

/**
 * Limit Shown Posts with Paid Memberships Pro Series Add On
 *
 * title: Limit Shown Posts with PMPro Series Add On
 * layout: snippet
 * collection: add-ons, pmpro-series
 * category: limit-posts
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 function my_pmpro_series_limit_posts_shown( $post_list, $series_object ) {
 	if ( ! is_user_logged_in() ) {
 		return $post_list;
 	}
 	global $current_user;
 	$member_days = pmpro_getMemberDays( $current_user->ID );
 	$posts_to_display = array();
 	foreach ( $post_list as $sp ) {
 		$posts_to_display[] = $sp;
 		if ( max( 0, $member_days ) < $sp->delay ) {
 			break;
 		}
 	}
 	return $posts_to_display;
 }
 add_filter( 'pmpro_series_post_list_posts', 'my_pmpro_series_limit_posts_shown', 10, 2 );
