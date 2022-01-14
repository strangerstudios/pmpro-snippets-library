<?php
/**
 * Hide or Show Fields on Member Profiles based on Membership Level
 * 
 * Requires Paid Membershps Pro v2.3.3+
 * 
 * title: Hide or Show Fields on Member Profiles based on Membership Level
 * layout: snippet
 * collection: misc
 * category: profile
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */


add_filter( 'pmpro_allow_weak_passwords', '__return_true' );