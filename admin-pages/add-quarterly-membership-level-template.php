<?php
/**
 * Add a quarterly membership level template option when adding a new membership level in the admin.
 *
 * title: Add Quarterly membership level template to the Membership Levels > Add New admin popup.
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
 * First, add the new template to the popup.
 */
function add_template_pmpro_membershiplevels_templates( $level_templates ) {
	// Adjust and uncomment this line if you want to remove an existing 
	$level_templates['quarterly'] = array(
		'name' => __( 'Quarterly', 'paid-memberships-pro' ),
		'description' => __( 'Set up a level that bills every 3 months.', 'paid-memberships-pro' ),
	);
	return $level_templates;
}
add_filter( 'pmpro_membershiplevels_templates', 'add_template_pmpro_membershiplevels_templates' );

/**
 * Then, configure level object defaults for the Quarterly template option.
 */
function quarterly_template_pmpro_membershiplevels_template_level( $level, $template ) {
	// Return earliy if this isn't the quarterly template.
	if ( $template != 'quarterly' ) {
		return $level;
	}

	// Set the defaults for the quarterly level.
	$level->initial_payment = 15;
	$level->billing_amount = 15;
	$level->cycle_number = 3;
	$level->cycle_period = 'Month';
	$level->billing_limit = NULL;
	$level->trial_amount = NULL;
	$level->trial_limit = NULL;
	$level->expiration_number = NULL;
	$level->expiration_period = NULL;

	// Return the level object.
	return $level;
}
add_filter( 'pmpro_membershiplevels_template_level', 'quarterly_template_pmpro_membershiplevels_template_level', 10, 2 );
