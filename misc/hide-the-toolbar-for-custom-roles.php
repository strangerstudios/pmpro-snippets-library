<?php
/**
 * Hide the WordPress Toolbar for custom roles
 *
 * title: Hide the WordPress Toolbar for custom roles pmpro_role_1 and pmpro_role_2.
 * layout: snippet
 * collection: core
 * category: customization, roles
 * link: https://www.paidmembershipspro.com/hide-the-wordpress-toolbar-for-additional-user-roles/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function hide_toolbar_for_custom_role( $hide ) {
	global $current_user;

	// Update this line with your custom roles.
	$roles_to_hide = array( 'pmpro_role_1', 'pmpro_role_2' );

	if ( ! empty( array_intersect( $roles_to_hide, $current_user->roles ) ) ) {
		$hide = true;
	}

	return $hide;
}
add_filter( 'pmpro_hide_toolbar', 'hide_toolbar_for_custom_role' );
