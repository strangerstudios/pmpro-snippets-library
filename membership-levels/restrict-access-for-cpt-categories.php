<?php
/**
 * Restrict access by Membership Level for Custom Post Type Categories
 * 
 * title: Restrict access by Membership Level for Custom Post Type Categories
 * layout: snippet
 * collection: membership-levels
 * category: cpt
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


function my_pmpro_term_template_redirect() {

    global $post;
    
    //change category and level ID here
    if( has_term( 'my_category', 'category', $post ) && !pmpro_hasMembershipLevel() ) {
        wp_redirect( pmpro_url( 'levels' ) );
        exit;
    }

}
add_action('template_redirect', 'my_pmpro_term_template_redirect');