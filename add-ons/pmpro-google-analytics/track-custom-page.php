<?php
/**
 * track a custom page and its level in pmpro-google-analytics
 *
 * title:track a custom page and its level.
 * layout: snippet
 * collection: add-ons, pmpro-google-analytics
 * category: analytics, custom page, custom checkout page
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add the level id of the level you want to track to the array of level ids to track. (See function below)
 *
 * @param array $track_levels The default array of level ids to track.
 * @return array $track_levels The custom array of level ids to track. 
 */ 
function my_pmproga4_track_level_ids_for_page( $track_levels ) {
	//add the levels id of the level you want to track
	$track_levels = array( 1, 2, 8 );
	return $track_levels;
}

/**
 * Hook into pmproga4_track_view_item_event to track a custom page.
 *
 * @return bool $return true if the page is the custom page, false otherwise.
 */
function my_pmproga4_track_view_item_event() {
	//edit this to the slug of your custom page as needed.
	$custom_page_to_track = 'custom-page';
	//Whether or not is the page we wan't to track.
	$is_custom_page = is_page( $custom_page_to_track );
	if( $is_custom_page ) {
		add_filter( 'pmproga4_track_level_ids', 'my_pmproga4_track_level_ids_for_page', 10, 2 );
	}
	return $is_custom_page;
}

add_filter( 'pmproga4_track_view_item_event', 'my_pmproga4_track_view_item_event', 10, 2 );
