<?php
/**
 * Customize the list of available countries at checkout
 *
 * title: Customize the list of available countries at checkout
 * layout: snippet
 * collection: checkout
 * category: registration-check
 * link: TBD
 *
 * This code snippet replaces the list of countries shown at checkout,
 * allowing you to limit signups to specific countries.
 *
 * The filtered country list also applies to other areas where
 * PMPro displays a country dropdown, including the Member Profile
 * Edit page and admin user profile screen.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Copy everything below into your custom plugin or Code Snippets plugin.
function pmproc_custom_countries( $pmpro_countries ) {
    // Create a list of countries you would like to show.
    // Refer to the full list of country codes here:
    // https://github.com/strangerstudios/paid-memberships-pro/blob/dev/includes/countries.php
    $pmpro_countries = array(
        'US' => 'United States',
        'CA' => 'Canada',
        'GB' => 'United Kingdom',
    );

    // Sort and return your countries.
    asort( $pmpro_countries );
    return $pmpro_countries;
}
add_filter( 'pmpro_countries', 'pmproc_custom_countries', 11 );