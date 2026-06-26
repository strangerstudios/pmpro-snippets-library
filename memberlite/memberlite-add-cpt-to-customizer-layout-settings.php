<?php
/**
 * Add a custom post type to Memberlite's per-type Customizer layout settings.
 * Memberlite includes popular PMPro CPTs by default. Use this recipe to expose
 * any additional CPT so its archive and single-view sidebar, column ratio, and
 * other layout options become individually configurable in the Customizer.
 *
 * Replace 'event' with the post type slug you want to add.
 *
 * title: Add a Custom Post Type to Memberlite's Customizer Layout Settings
 * layout: snippet
 * collection: memberlite
 * category: design
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_memberlite_add_cpt_to_customizer( $cpts ) {
	// Replace 'event' with your custom post type slug.
	$cpts[] = 'event';
	return $cpts;
}
add_filter( 'memberlite_customizer_cpts', 'my_memberlite_add_cpt_to_customizer' );
