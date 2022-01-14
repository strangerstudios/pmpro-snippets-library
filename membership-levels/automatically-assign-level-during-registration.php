<?php
/**
 * Assign a membership level to a user during the registration process.
 *
 * Change the Level ID on line 24 to your chosen default level.
 *
 * Note: This approach will not process any membership subscription payments.
 * It will only work if your default membership level is free.
 *
 * title: Assign a membership level to a user during the registration process.
 * layout: snippet
 * collection: membership-levels
 * category: registration
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Disables the pmpro redirect to levels page when user tries to register
add_filter( 'pmpro_login_redirect', '__return_false' );

function my_pmpro_default_registration_level( $user_id ) {

	// Give all members who register membership level 1
	pmpro_changeMembershipLevel( 1, $user_id );

}
add_action( 'user_register', 'my_pmpro_default_registration_level' );
