<?php // do not copy this line
/**
 * Example of how to add links to the Member Links list on the Membership Account page.
 *
 * Learn More at: https://www.paidmembershipspro.com/add-links-membership-level-membership-account-page-links-section/
 * 
 * title: Add Links to Members links on Members Account Page 
 * layout: snippet
 * collection: frontend-pages
 * category: members-links
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Add links to the top of the list
function my_pmpro_member_links_top() {
	//Add the level IDs here
	if ( pmpro_hasMembershipLevel( array(1,2,3 ) ) ) {
		//Add the links in <li> format here ?>
		<li><a href="/top/">My Member Link Top</a></li>
		<?php
	}
}
add_action( 'pmpro_member_links_top','my_pmpro_member_links_top' );

// Add links to the bottom of the list
function my_pmpro_member_links_bottom() {
	//Add the level IDs here
	if ( pmpro_hasMembershipLevel( array( 1,2,3 ) ) ) {
		//Add the links in <li> format here ?>
		<li><a href="/bottom/">My Member Link Bottom</a></li>
		<?php
	}
}
add_action( 'pmpro_member_links_bottom','my_pmpro_member_links_bottom' );