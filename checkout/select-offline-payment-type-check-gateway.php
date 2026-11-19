<?php
/**
 * Let members choose an offline payment method at checkout and show instructions for that method.
 *
 * title: Let Users Select Offline Payment Methods Using the Pay by Check Add On
 * layout: snippet
 * collection: checkout
 * category: check
 * link: https://www.paidmembershipspro.com/let-users-select-offline-payment-methods-using-the-pay-by-check-gateway/
 *
 * Requires the Pay by Check Add On with the payment gateway set to "Pay by Check".
 *
 * Edit the methods in my_pmpro_offline_payment_methods() below. For each method, the snippet adds:
 * - An option in a "How will you pay?" dropdown at checkout and on the member's profile.
 * - A conditional HTML field that shows that method's instructions when it is selected.
 *
 * The chosen method's instructions are added after the general payment instructions
 * (Memberships > Settings > Payment Gateway) on the confirmation page, the single order page,
 * and in the checkout and Pay by Check emails. Keep the general instructions generic, such as
 * "Your membership will be activated once we receive your payment."
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * The offline payment methods you accept.
 *
 * Keys are saved to the member's profile and order. Use lowercase letters, numbers, and underscores,
 * and don't change a key after members have chosen it.
 *
 * @return array
 */
function my_pmpro_offline_payment_methods() {
	return array(
		'check' => array(
			'label'        => 'Check',
			'instructions' => '<p><strong>Paying by check:</strong> Make your check payable to Example Organization and mail it to 123 Main Street, Anytown, ST 12345. Write your order number on the memo line.</p>',
		),
		'venmo' => array(
			'label'        => 'Venmo',
			'instructions' => '<p><strong>Paying with Venmo:</strong> Send your payment to @example-organization. Include your order number in the note.</p>',
		),
		'zelle' => array(
			'label'        => 'Zelle',
			'instructions' => '<p><strong>Paying with Zelle:</strong> Send your payment to payments@example.com. Include your order number in the memo.</p>',
		),
		'cash'  => array(
			'label'        => 'Cash',
			'instructions' => '<p><strong>Paying with cash:</strong> Bring your payment to the front desk during office hours, Monday through Friday, 9am to 5pm.</p>',
		),
	);
}

/**
 * Add the payment method dropdown and one conditional instructions field per method.
 */
function my_pmpro_offline_payment_method_fields() {
	// Don't break if PMPro is out of date or not loaded.
	if ( ! function_exists( 'pmpro_add_user_field' ) ) {
		return;
	}

	$methods = my_pmpro_offline_payment_methods();

	$options = array( '' => '- choose one -' );
	foreach ( $methods as $key => $method ) {
		$options[ $key ] = $method['label'];
	}

	$fields = array();

	$fields[] = new PMPro_Field(
		'offline_payment_method',
		'select',
		array(
			'label'          => 'How will you pay?',
			'options'        => $options,
			'profile'        => true,
			'memberslistcsv' => true,
		)
	);

	foreach ( $methods as $key => $method ) {
		$fields[] = new PMPro_Field(
			'offline_payment_method_' . $key . '_instructions',
			'html',
			array(
				'label'         => false,
				'showmainlabel' => false,
				'html'          => wp_kses_post( $method['instructions'] ),
				'profile'       => false,
				'depends'       => array( array( 'id' => 'offline_payment_method', 'value' => $key ) ),
			)
		);
	}

	pmpro_add_field_group( 'offline_payment_method', 'Payment Method' );
	foreach ( $fields as $field ) {
		pmpro_add_user_field( 'offline_payment_method', $field );
	}
}
add_action( 'init', 'my_pmpro_offline_payment_method_fields' );

/**
 * Require a payment method when checking out for a paid level with Pay by Check.
 *
 * @param bool $continue Whether the checkout should continue.
 * @return bool
 */
function my_pmpro_require_offline_payment_method( $continue ) {
	if ( ! $continue || 'check' !== pmpro_getGateway() ) {
		return $continue;
	}

	$level = pmpro_getLevelAtCheckout();
	if ( empty( $level ) || pmpro_isLevelFree( $level ) ) {
		return $continue;
	}

	$methods = my_pmpro_offline_payment_methods();
	$chosen  = isset( $_REQUEST['offline_payment_method'] ) ? sanitize_key( wp_unslash( $_REQUEST['offline_payment_method'] ) ) : '';
	if ( empty( $methods[ $chosen ] ) ) {
		pmpro_setMessage( 'Please choose how you will pay.', 'pmpro_error' );
		return false;
	}

	return $continue;
}
add_filter( 'pmpro_checkout_checks', 'my_pmpro_require_offline_payment_method' );

/**
 * Save the chosen payment method to the order.
 *
 * @param int         $user_id The user ID.
 * @param MemberOrder $order   The checkout order.
 */
function my_pmpro_save_offline_payment_method_to_order( $user_id, $order ) {
	if ( empty( $order->id ) || 'check' !== $order->gateway || ! isset( $_REQUEST['offline_payment_method'] ) ) {
		return;
	}

	$methods = my_pmpro_offline_payment_methods();
	$chosen  = sanitize_key( wp_unslash( $_REQUEST['offline_payment_method'] ) );
	if ( ! empty( $methods[ $chosen ] ) ) {
		update_pmpro_membership_order_meta( $order->id, 'offline_payment_method', $chosen );
	}
}
add_action( 'pmpro_after_checkout', 'my_pmpro_save_offline_payment_method_to_order', 5, 2 );

/**
 * Get the payment method for an order.
 *
 * Falls back to the member's profile for orders without one, such as Pay by Check renewal orders.
 *
 * @param MemberOrder $order The order.
 * @return array|false The method from my_pmpro_offline_payment_methods(), or false.
 */
function my_pmpro_get_order_offline_payment_method( $order ) {
	if ( empty( $order->id ) || 'check' !== $order->gateway ) {
		return false;
	}

	$key = get_pmpro_membership_order_meta( $order->id, 'offline_payment_method', true );
	if ( empty( $key ) && ! empty( $order->user_id ) ) {
		$key = get_user_meta( $order->user_id, 'offline_payment_method', true );
	}

	$methods = my_pmpro_offline_payment_methods();
	return empty( $methods[ $key ] ) ? false : $methods[ $key ];
}

/**
 * Add the method's instructions on the confirmation page and single order page.
 *
 * Both pages load the order into the $pmpro_invoice global.
 *
 * @param string $instructions The general payment instructions.
 * @return string
 */
function my_pmpro_offline_payment_instructions_on_pages( $instructions ) {
	global $pmpro_invoice;

	if ( is_admin() || empty( $pmpro_invoice ) || ! is_a( $pmpro_invoice, 'MemberOrder' ) ) {
		return $instructions;
	}

	$method = my_pmpro_get_order_offline_payment_method( $pmpro_invoice );
	if ( empty( $method ) ) {
		return $instructions;
	}

	return $instructions . "\n\n" . $method['instructions'];
}
add_filter( 'option_pmpro_instructions', 'my_pmpro_offline_payment_instructions_on_pages' );

/**
 * Add the method's instructions to emails that include !!instructions!! or {{ instructions }}.
 *
 * @param array      $data  The email data.
 * @param PMProEmail $email The email object.
 * @return array
 */
function my_pmpro_offline_payment_instructions_in_emails( $data, $email ) {
	if ( ! is_array( $data ) || ! isset( $data['instructions'] ) || empty( $data['order_id'] ) ) {
		return $data;
	}

	$order  = new MemberOrder( $data['order_id'] );
	$method = my_pmpro_get_order_offline_payment_method( $order );
	if ( empty( $method ) ) {
		return $data;
	}

	$data['instructions'] .= wp_kses_post( wpautop( $method['instructions'] ) );

	return $data;
}
add_filter( 'pmpro_email_data', 'my_pmpro_offline_payment_instructions_in_emails', 20, 2 );

/**
 * Show the chosen payment method when editing an order in the admin.
 *
 * @param MemberOrder $order The order.
 */
function my_pmpro_show_offline_payment_method_on_edit_order( $order ) {
	$method = my_pmpro_get_order_offline_payment_method( $order );
	if ( empty( $method ) ) {
		return;
	}
	?>
	<tr>
		<th scope="row" valign="top">Offline Payment Method</th>
		<td><?php echo esc_html( $method['label'] ); ?></td>
	</tr>
	<?php
}
add_action( 'pmpro_after_order_settings', 'my_pmpro_show_offline_payment_method_on_edit_order' );
