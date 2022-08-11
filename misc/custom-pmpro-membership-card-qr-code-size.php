<?php
/*
 * Adjust the size of the QR code on Membership Card.
 *
 * title: Adjust size of QR code on Membership Card
 * layout: snippet
 * collection: misc
 * category: qr-code
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function custom_pmpro_membership_card_qr_code_size( $size ) {
	$size = '50x50';
	return $size;
}
add_filter( 'pmpro_membership_card_qr_code_size', 'custom_pmpro_membership_card_qr_code_size' );
