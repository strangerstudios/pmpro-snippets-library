<?php
/**
 * Change the WordPress Users' Display Name using the Force First Last Plugin
 *
 * Requires the Force First Last Plugin (https://wordpress.org/plugins/force-first-last/)
 *
 * title: Change the display name to default to LAST FIRST using the Force First Last WordPress plugin.
 * layout: snippet
 * collection: misc
 * category: user
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_ffl_display_name_order( $display_name_order, $first_name, $last_name ) {

	$display_name_order = array( $last_name, $first_name );

	return $display_name_order;

}
add_filter( 'ffl_display_name_order', 'my_ffl_display_name_order', 15, 3 );
