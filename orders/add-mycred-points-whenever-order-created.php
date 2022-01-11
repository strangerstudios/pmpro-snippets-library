<?php
/**
 * Add myCred points every time an order is added to Paid Memberships Pro.
 * This will work for checkouts and automatic payments.
 * Configure lines 30-37 to assign points based on membership level ID.
 * 
 * title: Add myCred points when order is added to Paid Memberships Pro
 * layout: snippet
 * collection: orders
 * category: mycred
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function add_my_cred_to_pmpro_orders( $order ) {

    // Bail if order isn't successful.
    if ( ! function_exists( 'mycred_add' ) || ! in_array( $order->status, array( '', 'success' ) ) ) {
        return;
    }

    $level_id = intval( $order->membership_id );
    $user_id = intval( $order->user_id );
    $points = 10; // default to 0 points.
    $reference = 'Successful Membership Payment';
    $entry = 'Paid Memberships Pro - level: ' . $level_id;

    switch( $level_id ) {
        case 1:
            $points = 20;
            break;
        case 2:
            $points = 15;
            break;
    }

    //Add points to my cred
    mycred_add( $reference, $user_id, $points, $entry );

    // Write to order notes.
    $order->notes .= ' MyCred points for this order: ' . $points;
    $order->saveOrder();
}
add_action( 'pmpro_added_order', 'add_my_cred_to_pmpro_orders', 10, 1 );