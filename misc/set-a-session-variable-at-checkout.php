<?php
/**
 * Add a session variable so we only push the ecommerce data to GTM on the first checkout.
 * Not any subsequent visit to the confirmation page.
 *
 */
function pmpro_gtag_ecommerce_set_session( $user_id, $morder ) {
	pmpro_set_session_var( 'pmpro_gtag_order', $morder->id );
}
add_action( 'pmpro_after_checkout', 'pmpro_gtag_ecommerce_set_session', 10, 2 );
