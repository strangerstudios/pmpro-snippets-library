<?php
/**
 * Conditionally display Bricks Builder elements to membership levels based on element class
 * Select your element in the Bricks editor, go to Style > CSS > CSS Classes and enter a classname
 * Enter your classname on line 30 and level IDs on line 32
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

	// get element CSS classes
	$classes = $element->attributes['_root']['class'];

	// target element with class show-for-levels-1-2-3
	if( in_array( 'show-for-levels-1-2-3', $classes ) ) {
		// hide content if user doesn't have these level IDs
		if( ! pmpro_hasMembershipLevel( array( 1, 2, 3 ) ) ) {
			$render = false;
		}
	}

	return $render;

}
add_filter( 'bricks/element/render', 'my_bricks_show_content_for_pmpro_levels', 10, 2 );
