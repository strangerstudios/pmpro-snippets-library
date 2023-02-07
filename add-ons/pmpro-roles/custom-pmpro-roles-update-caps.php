<?php
/*
 * Update the capabilities for a level-generated role when using Paid Memberships Pro and the Roles for Membership Levels Add On.
 *
 * title: Update level-generated role capabilities with PMPro and Roles Add On
 * layout: snippet
 * collection: add-ons, pmpro-roles
 * category: capabilities
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function custom_pmpro_roles_update_caps() {
	$role = get_role( 'pmpro_role_30' );
	$role->add_cap( 'delete_posts', true );
	$role->add_cap( 'edit_posts', true );
	$role->add_cap( 'read', true );
}
add_action( 'wp', 'custom_pmpro_roles_update_caps' );
