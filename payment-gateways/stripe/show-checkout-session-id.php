<?php

/**
 * Show the Stripe Checkout Session ID when editing an order.
 *
 * title: Show Stripe Checkout Session ID
 * layout: snippet
 * collection: payment-gateways, stripe
 * category: libraries
 * link: https://www.paidmembershipspro.com/stripe-additional-payment-methods/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * If there is a Stripe Checkout session ID for an order, show it when editing the order.
 *
 * @param MemberOrder $order The order object being edited.
 */
function pmpro_after_order_settings_stripe_checkout_session_id( $order ) {
	$stripe_checkout_session_id = get_pmpro_membership_order_meta( $order->id, 'stripe_checkout_session_id', true );
	if ( empty( $stripe_checkout_session_id ) ) {
		return;
	}

	?>
	<tr class="pmpro_checkout_session_id">
		<th scope="row" valign="top">
			<label for="stripe_checkout_session_id"><?php esc_html_e( 'Stripe Checkout Session ID', 'pmpro-snippets-library' ); ?></label>
		</th>
		<td>
			<input type="text" id="stripe_checkout_session_id" name="stripe_checkout_session_id" value="<?php echo esc_attr( $stripe_checkout_session_id ); ?>" size="75"  readonly />
		</td>
	</tr>
	<?php
}
add_action( 'pmpro_after_order_settings', 'pmpro_after_order_settings_stripe_checkout_session_id' );
