<?php
/**
 * Send the Recurring Payment Receipt (Invoice) email on initial checkout.
 *
 * title: Send Members an Additional Invoice via Email after Membership Checkout
 * layout: snippet
 * collection: email
 * category: emails
 * link: https://www.paidmembershipspro.com/send-members-an-additional-invoice-via-email-after-membership-checkout/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_after_checkout_send_invoice_email( $user_id, $order ) {

	$user = get_user_by( 'id', $user_id );

	if ( ! $user ) {
		return;
	}

	$email = new PMProEmail();
	$email->sendInvoiceEmail( $user, $order );
}
add_action( 'pmpro_after_checkout', 'my_pmpro_after_checkout_send_invoice_email', 10, 2 );
