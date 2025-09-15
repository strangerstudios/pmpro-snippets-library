<?php
/**
 * Convert capital letters to lowercase and replace all spaces with an underscore when 
 * new users register via Paid Memberships Pro.
 * 
 * title: Reformat usernames to use allowed characters after checkout.
 * layout: snippet
 * collection: username
 * category: checkout
 * link: https://www.paidmembershipspro.com/reformat-usernames-to-use-allowed-characters-after-checkout/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_custom_user_registration_changes( $userdata ) {
	$userdata['user_login'] = str_replace( ' ', '_', strtolower( $userdata['user_login'] ) );
	return $userdata;
}
add_filter( 'pmpro_checkout_new_user_array', 'my_pmpro_custom_user_registration_changes', 10, 1 );
