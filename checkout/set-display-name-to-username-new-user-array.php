<?php
/*
 * Set the display name as the entered username for all users created through a Paid Memberships Pro checkout.
 *
 * title: Display name as entered username created through checkout
 * layout: snippet
 * collection: checkout
 * category: display username
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 *
 */
function set_display_name_to_username_new_user_array( $new_user_array ) {
	$new_user_array['display_name'] = $new_user_array['user_login'];
	return $new_user_array;
}
add_filter( 'pmpro_checkout_new_user_array', 'set_display_name_to_username_new_user_array' );
