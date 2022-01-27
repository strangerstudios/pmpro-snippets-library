<?php
/**
 * Create html templates in your child theme under paid-memberships-pro/email/ and use this snippet to force those templates to override those in the plugin.
 * 
 * title: Overwrite email templates from child theme HTML templates
 * layout: snippet-example
 * collection: email
 * category: templates
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function cg_pmpro_load_from_theme_template( $body, $email ) {

    if ( file_exists( get_stylesheet_directory() . '/paid-memberships-pro/email/' . $email->template . '.html' ) ) {
        $body = file_get_contents( get_stylesheet_directory() . '/paid-memberships-pro/email/' . $email->template . '.html' );
    }

    return $body;
}
add_filter( 'pmpro_email_body', 'cg_pmpro_load_from_theme_template', 15, 2 );
