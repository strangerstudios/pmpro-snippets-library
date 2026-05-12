<?php
/**
 * Show today's date in the Terms of Service label at Membership Checkout.
 *
 * title: Terms Of Service Add Today's Date
 * layout: snippet
 * collection: checkout
 * category: registration-check
 * link: https://www.paidmembershipspro.com/show-todays-date-in-the-terms-of-service-box-at-membership-checkout/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function add_todays_date_to_pmpro_tos_label( $tos_label, $tospage ) {
    // Get today's date in the site's preferred format.
    $todays_date = date( get_option( 'date_format' ) );

    // Modify the label to include today's date.
    $tos_label = sprintf(
        __( '%1$s on %2$s', 'pmpro-snippets-library' ),
        $tos_label,
        esc_html( $todays_date )
    );

    return $tos_label;
}
add_filter( 'pmpro_tos_field_label', 'add_todays_date_to_pmpro_tos_label', 10, 2 );
