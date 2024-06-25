<?php

/**
 * Adjust the output of the "Membership" tab for the BuddyPress Profile.
 * Learn more at https://www.paidmembershipspro.com/customize-buddypress-membership-tab/
 * You can see what sections to add or remove from here: 
 * https://www.paidmembershipspro.com/documentation/shortcodes/page-shortcodes/#account
 * This recipe adds only the Membership and Invoices section of the PMPro account page to the Membership Tab area on the BP profile page
 * 
 * title: Adjust Membership tab output on BuddyPress Profile
 * layout: snippet-example
 * collection: pmpro-buddypress
 * category: pmpro account, buddypress, buddyboss
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_buddypress_profile_account_shortcode( $content ) {
	$content = '[pmpro_account sections="membership,invoices"]';
	return $content;
}

add_filter( 'pmpro_buddypress_profile_account_shortcode', 'my_pmpro_buddypress_profile_account_shortcode' );
