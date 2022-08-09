<?php
/**
 * Remove one or more built-in level templates when adding a new membership level in the admin.
 *
 * This recipe has two examples: removing a single template from the built-in list OR the code to unset all built-in templates.
 *
 * title: Remove built-in level templates from the Membership Levels > Add New admin popup.
 * layout: snippet
 * collection: admin-pages
 * category: membership-levels
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Remove the "trial" template or unset all templates from the Membership Levels > Add New popup.
 *
 */
function remove_template_pmpro_membershiplevels_templates( $level_templates ) {
	// Remove the "Trial" level template.
	unset( $level_templates['trial'] );

	// Remove all level templates.
	//$level_templates = array();

	return $level_templates;
}
add_filter( 'pmpro_membershiplevels_templates', 'remove_template_pmpro_membershiplevels_templates' );
