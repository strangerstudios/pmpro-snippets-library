<?php
/**
 * Capture Unique Post Data as Custom Dimensions Using the Google Analytics Integration Add On
 *
 * title: Capture Unique Post Data as Custom Dimensions Using the Google Analytics Integration Add On
 * layout: snippet
 * collection: add-ons, pmpro-google-analytics
 * category: dimensions
 * link: https://www.paidmembershipspro.com/unique-post-data-ga/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 
/**
 * Customize dimensions array to add unique post tags to pmpro-google-analytics.
 * 
 * @param array $dimensions The default dimensions.
 * @return array $return The customized dimensions array.
 */
function my_pmproga4_default_custom_dimension( $dimensions ) {
	if ( is_single( ) ) {
		// Get the post tags.
		$post_tags = get_the_tags();
		
		if ( ! empty( $post_tags ) ) {
			// Use wp_list_pluck to get an array of tag names.
			$tag_names = wp_list_pluck( $post_tags, 'name' );
			$dimensions['tags'] = implode( ',', $tag_names );
		}
	}
	return $dimensions;
}
add_filter( 'pmproga4_default_custom_dimension', 'my_pmproga4_default_custom_dimension' );
