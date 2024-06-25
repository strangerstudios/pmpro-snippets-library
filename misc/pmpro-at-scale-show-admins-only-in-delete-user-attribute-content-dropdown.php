<?php
/**
 * Only include administrator users in the dropdown to reassign content from the delete user action.
 *
 * title: Only Include Admins in the WordPress User Delete Dropdown
 * layout: snippet
 * collection: misc
 * category: scaling
 * link: https://www.paidmembershipspro.com/admins-user-delete-dropdown/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Set query args for the users dropdown.
function my_pmpro_delete_users_query_args( $query_args, $args ) {
	if ( 'reassign_user' === $args['name'] ) {
		$query_args['role'] = 'administrator';
	}
	return $query_args;
}

// Add filter to the users dropdown if action is delete.
function my_pmpro_load_users_action() {
	// Make sure the "action" is "delete".
	if ( 'delete' !== filter_input( INPUT_GET, 'action' ) ) {
		return;
	}

	add_filter( 'wp_dropdown_users_args', 'my_pmpro_delete_users_query_args', 10, 2 );
}
add_action( 'load-users.php', 'my_pmpro_load_users_action' );
