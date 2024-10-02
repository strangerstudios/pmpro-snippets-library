<?php

/**
 * Run order creation checks before user creation at chekcout.
 * This prevents WP users from being created if there are issues in the checkout form.
 * Note that orders will still be created before the payment step even if the payment then fails.
 * Requires Paid Memberships Pro V3.2+ 
 * 
 * title: Prevent User Creation When There Are Issues in the Checkout Form
 * layout: snippet
 * collection: checkout
 * category: logo
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_run_order_checks_before_user_creation( $continue_registration ) {
    if ( empty( $continue_registration ) ) {
        return false;
    }

    return apply_filters( 'pmpro_checkout_order_creation_checks', true );
}
add_filter( 'pmpro_checkout_user_creation_checks', 'my_pmpro_run_order_checks_before_user_creation' );