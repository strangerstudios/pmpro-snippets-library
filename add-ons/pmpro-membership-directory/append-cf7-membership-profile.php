<?php
/**
 * Add Contact Form 7 to the Profile page when using the Member Directory and Profiles Add On for Paid Memberships Pro.
 *
 * Update line 36 with the correct CF7 shortcode for your desired form to display.
 * Add a hidden field to your form: "[hidden send-to-email default:shortcode_attr]".
 * Set the "To" field of the Contact Form to "[send-to-email]".
 * 
 * title: Add Contact Form 7 to the Profile page
 * layout: snippet
 * collection: add-ons, pmpro-membership-directory
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


// Allow custom shortcode attribute for "send-to-email". 
function custom_shortcode_atts_wpcf7_filter( $out, $pairs, $atts ) {

    $my_attr = 'send-to-email'; 

    if ( isset( $atts[$my_attr] ) ) {
        $out[$my_attr] = $atts[$my_attr];
    }

    return $out;

}
add_filter( 'shortcode_atts_wpcf7', 'custom_shortcode_atts_wpcf7_filter', 10, 3 );

// Add the contact form to the profile page using Contact Form 7.
function append_profile_page_with_cf7( $content ) {

    global $pmpro_pages;

    //Get the profile user

    if( ! function_exists( 'pmpromd_get_user' ) ) {
        return $content;
    }

    $pu = pmpromd_get_user();

    if ( ! empty( $pu ) && shortcode_exists( 'contact-form-7' ) && is_page( $pmpro_pages['profile'] ) ) {
        $content .= do_shortcode( '[contact-form-7 id="271" title="Contact form 1" send-to-email="' . $pu->user_email . '"]' );
    }

    return $content;

}
add_filter( 'the_content', 'append_profile_page_with_cf7' ); 