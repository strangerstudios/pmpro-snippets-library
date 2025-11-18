<?php
/**
 * Override the default "map failed to load" notice in PMPro Member Directory.
 * 
 * title: Override Map Failed Notice for Member Directory Map
 * layout: snippet
 * collection: pmpro-member-directory
 * category: maps, directory, profile
 * link: https://www.paidmembershipspro.com/overriding-the-default-membership-maps-error-message/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function mypmpromm_override_default_map_notice( $notice ) {
	return;
}
add_filter( 'pmpromd_map_failed_to_load_notice', 'mypmpromm_override_default_map_notice', 10, 1 );
