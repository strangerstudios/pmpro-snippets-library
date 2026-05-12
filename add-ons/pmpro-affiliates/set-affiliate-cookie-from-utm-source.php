<?php
/**
 * Auto-assign a PMPro affiliate cookie based on incoming utm_source.
 * Only sets the cookie if a matching, enabled affiliate code exists.
 *
 * title: Auto-assign affiliate cookie from utm_source
 * layout: snippet
 * collection: add-ons
 * category: pmpro-affiliates
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_affiliates_assign_pa_by_utm_source() {
    global $wpdb;
    if ( empty( $_GET['utm_source'] ) || ! empty( $_COOKIE['pmpro_affiliate'] ) ) {
        return;
    }

    $code = sanitize_text_field( wp_unslash( $_GET['utm_source'] ) );

    $affiliate = $wpdb->get_row( $wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}pmpro_affiliates WHERE code = %s AND enabled = '1'",
        $code
    ) );

    if ( $affiliate ) {
        setcookie( 'pmpro_affiliate', $code, time() + ( 30 * DAY_IN_SECONDS ), '/' );
        $_COOKIE['pmpro_affiliate'] = $code;
    }
}
add_action( 'init', 'my_pmpro_affiliates_assign_pa_by_utm_source' );
