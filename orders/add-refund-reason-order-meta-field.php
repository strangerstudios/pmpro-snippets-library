<?php
/**
 * Add an order meta field for "Refund Reason" so admin can track
 * why people are asking for a refund.
 * Configure lines 30-37 to assign points based on membership level ID.
 *
 * title: Add a Refund Reason Order Meta Fields for Admins
 * layout: snippet
 * collection: orders
 * category: refunds
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_refund_reason_after_order_settings( $order ) {
	if ( empty( $order->id ) ) {
		// This is a new order.
		return;
	}

	// Get the Refund Reason from Order Meta.
	$this_refund_reason = get_pmpro_membership_order_meta( $order->id, 'pmpro_refund_reason', true );
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Reason for Refund', 'pmpro-site-customizations' ); ?></th>
		<td>
			<?php
				$refund_reasons = array(
					'0' => '-- Select -- ',
					'changed-mind' => 'Changed Their Mind',
					'not-interested' => 'No Longer Interested',
					'no-reason' => 'No Reason',
					'complicated' => 'Too Complicated',
					'expensive' => 'Too Expensive',
				);
			?>
				<select id="pmpro_refund_reason" name="pmpro_refund_reason">
					<?php foreach ( $refund_reasons as $key => $label ) { ?>
						<option
							value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $this_refund_reason ); ?>><?php echo esc_html( $label ); ?></option>
					<?php } ?>
				</select>
		</td>
	</tr>
	<?php
}
add_action( 'pmpro_after_order_settings', 'my_pmpro_refund_reason_after_order_settings', 10, 1 );

/**
 * Save Refund Reason to Order Meta.
 */
function my_pmpro_refund_reason_updated_order( $order ) {
	// Save extra fields.
	if ( is_admin() && $_REQUEST['page'] === 'pmpro-orders' && ! empty( $_REQUEST['save'] ) ) {
		if ( isset( $_REQUEST['pmpro_refund_reason'] ) ) {
			update_pmpro_membership_order_meta( $order->id, 'pmpro_refund_reason', ( $_REQUEST['pmpro_refund_reason'] ) );
		}
	}
}
add_action( 'pmpro_updated_order', 'my_pmpro_refund_reason_updated_order' );
