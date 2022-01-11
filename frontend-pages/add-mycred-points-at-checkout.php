<?php
/**
 * Add myCred points at checkout based on membership level ID. 
 * Optionally apply points only to members that select a recurring membership.
 * 
 * Extend the code below to match your site's level ID's and amount of points for each level. 
 * This only applies points during the checkout process and not recurring orders.
 * 
 * title: Award myCred points during Paid Memberships Pro checkout.
 * layout: snippet
 * collection: frontend-pages
 * category: checkout, mycred
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function add_my_cred_to_pmpro_checkout( $user_id, $morder ) {
	// Bail if myCred doesn't exist.
	if ( ! function_exists( 'mycred_add' ) ) {
		return;
	}

	// Get the user's level and expiration date.
	$level = pmpro_getMembershipLevelForUser( $user_id );
	$level_expiration = $level->enddate;

	// Add these lines to give points to recurring members only.
	if ( ! empty( $level->enddate ) ) {
		return;
	}

    $points = 10; // default to 10 points. If level ID is blank 10 points would be awarded.
    $reference = 'Successful Membership Payment';
    $entry = 'Paid Memberships Pro - level: ' . $level->ID;
    switch( $level->ID ) {
        case 1:
            $points = 20;
            break;
        case 2:
            $points = 15;
            break;
    }

    // Add points to myCred.
    mycred_add( $reference, $user_id, $points, $entry );
    
    // Write to order notes.
    $morder->notes .= ' MyCred points for this order: ' . $points;
    $morder->saveOrder();
    
}
add_action( 'pmpro_after_checkout', 'add_my_cred_to_pmpro_checkout', 10, 2 );