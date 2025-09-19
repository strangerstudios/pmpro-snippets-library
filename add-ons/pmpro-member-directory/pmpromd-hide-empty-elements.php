<?php
/**
 * Hides empty elements on the directory and profile pages.
 *
 * Can be useful if you have a select type user field
 * and you don't want to display them if they are empty.
 * This requires the first select field option to be configured
 * with no value set, a colon, and a label set. e.g. ":label".
 *
 * For example:
 * :-- Select One --
 * canine:Dog
 * feline:Cat
 *
 * title: Hide empty elements in membership directory
 * layout: snippet
 * collection: frontend-pages
 * category: membership-directory
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpromd_hide_empty_elements( $value, $element, $pu, $displayed_levels ) {
	if ( empty( $value ) ) {
		return null;
	}
	return $value;
}
add_filter( 'pmpromd_get_display_value', 'my_pmpromd_hide_empty_elements', 10, 4 );