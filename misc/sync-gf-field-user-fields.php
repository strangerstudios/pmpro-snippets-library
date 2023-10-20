<?php

/**
 * Sync a Gravity Form Field to a PMPro User Field on Form Submission and Assign a Membership Level to the User
 * This gist requires the Gravity Forms User Registration Add-On to work
 *
 * title: Sync Gravity Forms text field to PMPro User Field
 * layout: snippet
 * collection: misc
 * category: Gravity Forms
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function mypmpro_connect_gf_to_userfields($user_id, $feed, $entry, $user_pass) {
    
    // Get the form ID
    $form_id = 1; // Replace with your actual form ID

    /**
     * Give the user a level
     */
     
    if( function_exists( 'pmpro_changeMembershipLevel' ) ) {
        pmpro_changeMembershipLevel( 2, $user_id ); //Give the user a membership level ID of 2
    }

    /*
     * Use a Gravity Forms TEXT Field and update it to work with a 
     * PMPro User Fields field
     */

    //The text field ID
    $text_input_id = 4;

    //We get the text field value
    $my_text_field_val = rgar( $entry, $text_input_id );

    //Was the field filled out? If so, lets use it. 
    if( ! empty( $my_text_field_val ) ) {
        //We update the field value to something PMPro can read (my_fancy_field)
        update_user_meta( $user_id, 'my_fancy_field', $my_text_field_val );
    }

 }

 add_action( 'gform_user_registered', 'mypmpro_connect_gf_to_userfields', 10, 4 );
