<?php
/**
 * Make sure authors can always see their own posts.
 *
 * title: Author can see their own post even if not member.
 * layout: snippet
 * collection: misc
 * category: access
 * url: https://www.paidmembershipspro.com/allow-authors-to-view-their-posts-regardless-of-membership-level/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_has_membership_access_filter_for_authors( $hasaccess, $mypost, $myuser, $post_membership_levels ) {
	if ( $mypost->post_author == $myuser->ID ) {
		$hasaccess = true;
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'my_pmpro_has_membership_access_filter_for_authors', 30, 4 );
