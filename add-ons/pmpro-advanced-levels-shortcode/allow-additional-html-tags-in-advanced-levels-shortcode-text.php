<?php
/*
 * Example on allowing use of additional HTML tags in the Advanced Levels Page Shortcode
 * description and expiration texts
 * 
 * This example will allow H1 tags, class attributes on H1 tags, and H2 tags
 * 
 * title: Allow use of additional HTML tags in the Advanced Levels Page Shortcode text
 * layout: snippet
 * collection: add-ons
 * category: pmpro-advanced-levels-shortcode
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/ 
 */

function my_pmproal_allow_html_tags( $allowed_html ) {
    $html = array(
        'h1' => array(
            'class' => array(),
        ),
        'h2' => array(),
    );

    $allowed_html = array_merge( $allowed_html, $html );

    return $allowed_html;
}
add_filter( 'pmproal_allowed_html', 'my_pmproal_allow_html_tags' );