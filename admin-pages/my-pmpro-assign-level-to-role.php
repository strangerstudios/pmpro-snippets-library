<?php
/**
 * Assign a LEVEL to a unique role
 *
 * title: Assing level to unique role.
 * layout: snippet
 * collection: admin-pages
 * category: assign-level
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


function assign_pmpro_level_to_role($user_id, $role, $old_roles)
{
	//we found a role related to pmpro level
	if($role == "yourrole")
	{
		pmpro_changeMembershipLevel(1, $user_id);
	}
}

add_action('set_user_role', 'assign_pmpro_level_to_role', 10, 3);
