<?php
/**
 * Sort the membership directory by lastname first, then first name.
 *
 * title: Sort members in Member Directory by last name, first name.
 * layout: snippet
 * collection: add-ons, pmpro-membership-directory
 * category: directory, sort, SQL
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function pmpro_custom_directory_sql_parts( $parts ) {
	$parts['ORDER'] = ' ORDER BY last_name,first_name ASC ';
	return $parts;
}
 add_filter( 'pmpro_member_directory_sql_parts', 'pmpro_custom_directory_sql_parts', 10, 1 );
