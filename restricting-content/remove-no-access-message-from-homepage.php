<?php
/**
 * Remove the restricted messages shown to non-members from the homepage.
 * 
 * title: Remove the restricted messages shown to non-members from the homepage.
 * layout: snippet
 * collection: restricting-content
 * category: content, restriction, non-member
 * link: https://www.paidmembershipspro.com/remove-restricted-messages/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_remove_no_access_message_from_homepage( $no_access_message_html ) {
	// Check if we are on the homepage.
	if ( is_front_page() || is_home() ) {
		// Return an empty string.
		return '';
	}

	// Otherwise, return the default no access message html.
	return $no_access_message_html;
}
add_filter( 'pmpro_no_access_message_html', 'my_pmpro_remove_no_access_message_from_homepage', 10, 1 );
