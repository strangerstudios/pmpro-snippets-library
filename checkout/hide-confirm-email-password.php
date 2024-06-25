<?php
/**
 * Hide the Confirm Email and Confirm Password Fields on the checkout page.
 *
 * title: Hide the Confirm Email and Confirm Password Fields
 * layout: snippet
 * collection: checkout
 * category: ui
 *
 * Hide the Confirm Email and Confirm Password Fields on the checkout page.
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

add_filter( 'pmpro_checkout_confirm_password', '__return_false' );
add_filter( 'pmpro_checkout_confirm_email', '__return_false' );