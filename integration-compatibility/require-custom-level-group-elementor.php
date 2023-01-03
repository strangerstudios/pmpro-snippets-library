<?php
/**
 * Create a group of membership levels to protect Elementor content with fewer settings and less need for future updates.
 *
 * title: Group membership levels to more easily protect content in Elementor.
 * layout: snippet
 * collection: integration-compatibility
 * category: content, restriction, elementor
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add the "My Custom Levels Group" item to the PMPro field in Elementor editor.
 */
function my_pmpro_elementor_levels_array_custom( $levels ) {
    // Add a new selection to the Require Membership Level advanced setting in Elementor.
    $levels['my_pmpro_elementor_custom_levels'] = __( 'My Custom Levels Group' );

    // Return the array of items for the dropdown with our new item added.
    return $levels;
}
add_filter( 'pmpro_elementor_levels_array', 'my_pmpro_elementor_levels_array_custom', 10, 1 );

/**
 * Check the custom setting against our specific level IDs before showing content.
 */
function my_pmpro_elementor_has_access_custom( $access, $element, $restricted_levels ) {
    // Check if the element is restricted for our custom level group item.
    if ( in_array( 'my_pmpro_elementor_custom_levels', $restricted_levels ) ) {
        // Require membership level IDs 1, 2, or 3 to view this content.
        if ( pmpro_hasMembershipLevel( array( 1, 2, 3 ) ) ) {
            return true;
        }
    }

    // Return whether the user has access to this content.
    return $access;
}
add_action( 'pmpro_elementor_has_access', 'my_pmpro_elementor_has_access_custom', 10, 3 );
