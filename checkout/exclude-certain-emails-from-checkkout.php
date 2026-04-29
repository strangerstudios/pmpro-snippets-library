<?php
/**
 * Exclude Certain Email Domains from Membership Signup
 * use this code recipe to prevent registration for specific email domains. 
 * edit the line 62 $invalid_domains array to include the domains you want to block.
 * 
 * title: Exclude Certain Email Domains from Membership Signup
 * layout: snippet
 * collection: checkout
 * category: restrict checkout
 * link: https://www.paidmembershipspro.com/exclude-certain-email-domains-from-membership-signup/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function invalid_email_addresses_pmpro_registration_checks( $okay ) {
    // Safely get the email entered at checkout.
    $email = '';
    if ( isset( $_POST['bemail'] ) ) {
        $email = sanitize_email( $_POST['bemail'] );
    }

    // Skip if no email provided (prevents PHP warnings).
    if ( empty( $email ) ) {
        return $okay;
    }

    // Check if the email domain is invalid.
    if ( my_checkForInvalidDomain( $email ) ) {
        global $pmpro_msg, $pmpro_msgt;
        $pmpro_msg = "Please enter a valid email address.";
        $pmpro_msgt = "pmpro_error";
        return false;
    }

    return $okay;
}
add_filter( 'pmpro_registration_checks', 'invalid_email_addresses_pmpro_registration_checks' );

/**
 * Check if email domain is in the blocked list.
 */
function my_checkForInvalidDomain( $email ) {
    $email = sanitize_email( $email );

    // Split the email into user and domain parts.
    $parts = explode( '@', $email );
    if ( count( $parts ) < 2 ) {
        return false;
    }

    $domain_part = strtolower( trim( end( $parts ) ) );

    // Remove any "+alias" portion from the username part.
    $user_part = preg_replace( '/\+.*/', '', $parts[0] );
    $normalized_email = $user_part . '@' . $domain_part;

    // Define domains to block.
    $invalid_domains = array( 'aol.com', 'yopmail.com' );

    // Check if domain ends with any blocked domain.
    foreach ( $invalid_domains as $invalid_domain ) {
        $invalid_domain = strtolower( str_replace( '*.', '', $invalid_domain ) );
        if ( str_ends_with( $domain_part, $invalid_domain ) ) {
            return true;
        }
    }

    return false;
}