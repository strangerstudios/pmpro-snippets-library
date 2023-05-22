<?php
/**
 * Customize the default confirmation message shown to new members
 *
 * title: Customize the default confirmation message shown to new members
 * layout: snippet
 * collection: membership-levels
 * category: confirmation
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_confirmation_message( $message ) {
	$message = '<p>This is a new confirmation message.</p>';

	return $message;
}
add_filter( 'pmpro_confirmation_message', 'my_pmpro_confirmation_message', 15, 1 );