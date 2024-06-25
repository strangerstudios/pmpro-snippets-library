<?php
/**
 * Removes profile action links on the account page.
 * 
 * title: Removes profile action links on the account page.
 * layout: snippet
 * collection: frontend-pages
 * category: profile, account
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_account_profile_action_links_removal( $pmpro_profile_action_links ) {

	unset( $pmpro_profile_action_links['edit-profile'] ); // Remove change password

	/* Uncomment the relative line by removing the comment ("//" double forward slash before the variable) if you would like to remove the links for password reset or logout. */
	// unset( $pmpro_profile_action_links['change-password'] ); // Remove change password
	// unset( $pmpro_profile_action_links['logout'] ); // Remove logout link

	return $pmpro_profile_action_links;
}
add_filter( 'pmpro_account_profile_action_links', 'my_pmpro_account_profile_action_links_removal', 15, 1 );