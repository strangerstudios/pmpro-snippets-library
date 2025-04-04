<?php
/**
 * Conditionally display Bricks Builder elements to membership levels based on element class
 * Select your element in the Bricks editor, go to Style > CSS > CSS Classes and enter a classname
 *
 *  title: Display elements to membership levels based on element class
 *  layout: snippet
 *  collection: integration-compatibility
 *  category: content, bricks builder
 *  link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_bricks_show_content_for_pmpro_levels( $render, $element ) {

	// Bail if PMPro isn't active or no CSS classes on element
	if ( ! function_exists( 'pmpro_hasMembershipLevel' ) || empty( $element->attributes['_root']['class'] ) ) {
		return $render;
	}

	// Enter your level IDs here to show content to.
	$levels = array( 1, 2, 3 );

	// Target every element with class .show-for-levels-1-2-3
	$my_class_name = 'show-for-levels-1-2-3';

	// get Bricks element CSS classes
	$classes = $element->attributes['_root']['class'];

	// target element with class show-for-levels-1-2-3
	if ( in_array( $my_class_name, $classes ) ) {
		// hide content if user doesn't have the defined level IDs.
		if ( ! pmpro_hasMembershipLevel( $levels ) ) {
			$render = false;
		}
	}

	return $render;

}
add_filter( 'bricks/element/render', 'my_bricks_show_content_for_pmpro_levels', 10, 2 );
