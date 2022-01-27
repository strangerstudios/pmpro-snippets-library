<?php
/**
 * Provide a basic implementation of the hCAPTCHA library on the checkout page. 
 * 
 * title: Provide a basic implementation of the hCAPTCHA library on the checkout page. 
 * layout: snippet
 * collection: checkout
 * category: spam
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


/**
 * Add the hCAPTCHA field to the checkout form. Replace SITE_KEY with your own Site Key
 * 
 */
function mypmpro_hcaptcha_add_form() {

    ?>
    
    <div class="h-captcha" data-sitekey="SITE_KEY"></div>   
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>

    <?php

}
add_action( 'pmpro_checkout_before_submit_button', 'mypmpro_hcaptcha_add_form' );

/**
 * Verifies the hCAPTCHA response. Replace SECRET with your own Secret Key
 */
function mypmpro_hcaptcha_process( $okay ) {

    if( !empty( $_REQUEST['h-captcha-response'] ) ) {

        $request = wp_remote_post( 'https://hcaptcha.com/siteverify', array( 'body' => array(
            'secret' => 'SECRET',
            'response' => $_REQUEST['h-captcha-response']
        ) ) );

        $response = wp_remote_retrieve_body( $request );

        $response = json_decode( $response );

        if( !empty( $response ) ) {
            if( !$response->success ) {
                //Validation failed - set an error message
                pmpro_setMessage("An error occured while verifying if you are human. Please try again.", "pmpro_error");
            }

            return $response->success;
        }
    }

    return $okay;

}
add_action( 'pmpro_registration_checks', 'mypmpro_hcaptcha_process' );