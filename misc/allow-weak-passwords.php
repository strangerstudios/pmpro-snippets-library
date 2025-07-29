<?php
/**
 * Allow weak passwords on the PMPro "Change Password" and "Password Reset" pages
 ** Learn more at https://www.paidmembershipspro.com/allow-weak-passwords/
 * Requires Paid Memberships Pro v2.3.3+
 *
 * title: Allow weak passwords during password reset or profile updates
 * layout: snippet
 * collection: misc
 * category: profile
 * link: https://www.paidmembershipspro.com/allow-members-use-weak-passwords/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
add_filter( 'pmpro_allow_weak_passwords', '__return_true' );
