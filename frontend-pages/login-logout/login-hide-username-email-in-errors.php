<?php
/**
 * When using PMPro login page,remove usernames and email from error messgages.
 *
 * title: Remove username and email from login error messages
 * layout: snippet
 * collection: login
 * category: login, error, text change
 * link: 
 *
 * Update lines 21,24,and 27 with your custom message.
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function pmpro_custom_login_errors($translated_text, $text, $domain) {
    if ($domain === 'paid-memberships-pro') {
        if ($text === '<strong>Error:</strong> The username <strong>%s</strong> is not registered on this site. If you are unsure of your username, try your email address instead.') {
            return 'Your Login Information is incorrect. Please try again.';
        }
        if ($text === '<strong>Error:</strong> The password you entered for the username %s is incorrect.') {
            return 'Your Login Information is incorrect. Please try again.';
        }
        if ($text === 'Unknown email address. Check again or try your username.') {
            return 'Your Login Information is incorrect. Please try again.';
        }
    }
    return $translated_text;
}
add_filter('gettext', 'pmpro_custom_login_errors', 10, 3);