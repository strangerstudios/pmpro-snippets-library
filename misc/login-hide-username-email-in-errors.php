<?php
/**
 * Filter the error message shown on the default WordPress login page AND the PMPro login page.
 *
 * title: Remove username and email from login error messages
 * layout: snippet
 * collection: misc
 * category: login, error, text change
 * link: https://www.paidmembershipspro.com/customize-login-error-messages-to-boost-account-security/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Filter the default WordPress login error message.
function my_custom_login_error_message( $error ) {
    return 'There was an error with the login details provided. Please try again.';
}
add_filter( 'login_errors', 'my_custom_login_error_message' );

// Filter the PMPro login error message.
function pmpro_custom_login_errors( $translated_text, $text, $domain ) {
    if ( $domain === 'paid-memberships-pro' ) {
        if ( $text === '<strong>Error:</strong> The username <strong>%s</strong> is not registered on this site. If you are unsure of your username, try your email address instead.' ) {
            return 'There was an error with the login details provided. Please try again.';
        }

        if ( $text === '<strong>Error:</strong> The password you entered for the username %s is incorrect.' ) {
            return 'There was an error with the login details provided. Please try again.';
        }

        if ( $text === 'Unknown email address. Check again or try your username.' ) {
            return 'There was an error with the login details provided. Please try again.';
        }
    }
    return $translated_text;
}
add_filter( 'gettext', 'pmpro_custom_login_errors', 10, 3 );
