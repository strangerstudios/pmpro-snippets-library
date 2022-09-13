<?php
/**
 * Redirect members of level ID 1 to the CPT with ID '232' on login.
 *
 * title: Redirect members of ID 1 to CPT ID 232
 * layout: snippet
 * collection: add-ons, member-homepage
 * category: members, redirect
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function my_cpt_pmpro_member_homepage_id( $member_homepage_id, $level_id ) {
 	// Only filter for Level 1.
 	if ( $level_id === '1' ) {
 		// Redirect to the post ID 232.
 		$member_homepage_id = '232';
 	}
 	return $member_homepage_id;
 }
 add_filter( 'pmpro_member_homepage_id', 'my_cpt_pmpro_member_homepage_id', 10, 2 );
