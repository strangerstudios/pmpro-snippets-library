<?php
/**
 * Capture Unique Post Data as Custom Dimensions Using the Google Analytics Integration Add On
 *
 * title: Capture Unique Post Data as Custom Dimensions Using the Google Analytics Integration Add On
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
 * Customize dimensions array to add unique post tags to pmpro-google-analytics.
 * 
 * @param array $dimensions The default dimensions.
 * @return array $return The customized dimensions array.
 */
function my_pmproga4_default_custom_dimension( $dimensions ) {
	//Change the post ID to the ID of your unique post
	$my_unique_id = 287;
	if ( is_single( $my_unique_id ) ) {
		//change the post ID to the ID of your unique post
		$my_unique_post = get_post( $my_unique_id );
		//Get the tag from the post
		$posttags = get_the_tags( $my_unique_post );

		// Use wp_list_pluck to get an array of tag names
		$tag_names = wp_list_pluck($posttags, 'name');

		$dimensions = array(
			'post_type' => '',
			'author' => '',
			'category' => '',
			'tags' => $tag_names,
		);

		$dimensions =  array ('post_type' => '',  'author' => '', 'category' => '', 'tags' => $posttags );
	}
	return $dimensions;
}

add_filter( 'pmproga4_default_custom_dimension', 'my_pmproga4_default_custom_dimension' );