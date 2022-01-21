<?php
/**
 * Redirect Members to a Unique Confirmation Page Based on Membership Level
 * 
 * title: Redirect Members to a Unique Confirmation Page Based on Membership Level
 * layout: snippet
 * collection: checkout
 * category: confirmation
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_confirmation_url( $rurl, $user_id, $pmpro_level ) {

    if( pmpro_hasMembershipLevel( 1 ) ) {
        $rurl = "http://example.com/page_1";
    } else if( pmpro_hasMembershipLevel( 2 ) ) {
        $rurl = "http://example.com/page_2";
    } else if( pmpro_hasMembershipLevel( 3 ) ) {
        $rurl = "http://example.com/page_3";
    }
    
    return $rurl;

}

add_filter('pmpro_confirmation_url', 'my_pmpro_confirmation_url', 10, 3);