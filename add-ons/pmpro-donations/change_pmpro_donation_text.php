<?php
/**
 * This function simply edits the text 'Make a Gift' for PMPRO Donations Add-On
 * 
 * title: Change "Make a Gift" Text for PMPRO Donations Add-On
 * layout: snippet
 * collection: add-ons, pmpro-donations
 * category: users, custom user fields
 * link: https://www.paidmembershipspro.com/add-ons/donations-add-on/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


function pmpro_donations_change_text( $change_text, $text, $domain ) {
    switch ( $change_text ) {
        case 'Make a Gift' :
            $change_text = __( 'Your Replacement Text Here', 'pmpro-donations' ); //edit 'This will change Make A Gift' to edit the text output of 'Make a Gift'
            break;
    }
    return $change_text;
}
add_filter( 'gettext', 'pmpro_donations_change_text', 20, 3 );