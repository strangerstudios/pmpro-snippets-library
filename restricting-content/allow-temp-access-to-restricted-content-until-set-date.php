<?php
/**
 * Allow temporary access to all restricted content for non-members until a specific date.
 * This recipe grants sitewide access to restricted posts or pages for visitors without a membership,
 * making it useful for limited-time promotions, site previews, or pre-launch content access.
 * After the set date, membership restrictions automatically apply again.
 *
 * title: Allow Temporary Access for Non-Members Until a Specific Date
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

function pmpro_allow_temp_access_for_non_members($hasaccess, $mypost, $myuser, $post_membership_levels){

	// Do not affect existing memberships access to posts.
	if( pmpro_hasMembershipLevel() ){
		return $hasaccess;
	}

	$end_date = strtotime( date( '2026-12-31' ) ); //change this date value. Format ( Y-m-d ).
	$today = strtotime( date( 'Y-m-d' ) );

	//get the difference in date.
	$diff = $end_date - $today;
	
	// If end date is today or earlier, grant access - if today > end date, return false.
	if( $diff >= 0 ){
		$hasaccess = true;
	}else{
		$hasaccess = false;
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'pmpro_allow_temp_access_for_non_members', 30, 4 );