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
 * Hook into pmproga4_track_view_item_event to track a custom page.
 *
 * @return bool $return true if the page is the custom page, false otherwise.
 */
function my_pmproga4_track_view_item_event() {
	//edit this to the slug of your custom page
	$is_custom_page = is_page( 'custom-page' );
	if ( $is_custom_page ) {
		add_filter( 'pmproga4_track_level_ids', 'my_pmproga4_track_level_ids', 10, 2 );

	}
	return $is_custom_page;
}

/**
 * Add the level id of the level you want to track to the array of level ids to track.
 *
 * @return array $level_ids The array of level ids to track. 
 */ 
function my_pmproga4_track_level_ids() {
	//add the level id of the level you want to track
	$level_ids[] = 6;
	return $level_ids;
}

add_filter( 'pmproga4_track_view_item_event', 'my_pmproga4_track_view_item_event', 10, 2 );