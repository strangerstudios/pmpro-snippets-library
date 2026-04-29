<?php
/**
 * Prioritize specific Nav Menu when a user has multiple memberships.
 * This recipe requires the Member-Specific Nav Menus Add On.
 *
 * title: Assign priority nav menu for users with multiple memberships
 * layout: snippet
 * collection: pmpro-nav-menus
 * category: navigation, multiple levels
 * link: https://www.paidmembershipspro.com/nav-menus-add-on-mmpu-multiple-memberships-per-user/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_nav_menu_priority( $levels ) {
    $levels = array( 5 ); // If the member has multiple levels, give the level ID 5 for the nav menu priority over other levels.
    return $levels;
}
add_filter( 'pmpronm_prioritize_levels', 'my_pmpro_nav_menu_priority', 10, 1 );