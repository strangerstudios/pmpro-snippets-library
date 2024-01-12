<?php
/**
 * Remove element from the default dimensions array in pmpro-google-analytics
 *
 * title: Remove an element from the default dimensions array in pmpro-google-analytics
 * layout: snippet
 * collection: add-ons, pmpro-google-analytics
 * category: dimensions
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 
/**
 * Remove category from the default dimensions array.
 *
 * @param array $dimensions The default dimensions array.
 * @return array $dimensions The customized dimensions array.
 */
function my_pmproga4_default_custom_dimension( $dimensions ) {
	// Remove the category dimension. Change the unset dimensiion as needed.
	unset( $dimension['category'] );
	return $dimensions;
}

add_filter( 'pmproga4_default_custom_dimension', 'my_pmproga4_default_custom_dimension' );