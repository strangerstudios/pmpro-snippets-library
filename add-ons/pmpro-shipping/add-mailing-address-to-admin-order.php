<?php
/**
 * Display the member's mailing address on the new admin order view screen (PMPro 3.6+).
 * Requires the Mailing Address Add On:
 * https://www.paidmembershipspro.com/add-ons/shipping-address-membership-checkout/
 *
 * title: Add Mailing Address to Admin Order View
 * layout: snippet
 * collection: add-ons, pmpro-shipping
 * category: orders, admin
 * link: https://www.paidmembershipspro.com/display-mailing-address-orders/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method:
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_add_mailing_address_to_admin_view_order_view( $order ) {
	?>
	<div class="pmpro_section">
		<div class="pmpro_section_toggle">
			<button class="pmpro_section-toggle-button" type="button" aria-expanded="true">
				<span class="dashicons dashicons-arrow-up-alt2"></span>
				<?php esc_html_e( 'Mailing Address', 'paid-memberships-pro' ); ?>
			</button>
		</div>

		<div class="pmpro_section_inside">
			<?php
			$user_id = $order->user_id;

			$firstname = get_user_meta( $user_id, 'pmpro_sfirstname', true );
			$lastname  = get_user_meta( $user_id, 'pmpro_slastname', true );
			$address1  = get_user_meta( $user_id, 'pmpro_saddress1', true );
			$address2  = get_user_meta( $user_id, 'pmpro_saddress2', true );
			$city      = get_user_meta( $user_id, 'pmpro_scity', true );
			$state     = get_user_meta( $user_id, 'pmpro_sstate', true );
			$zip       = get_user_meta( $user_id, 'pmpro_szipcode', true );
			$country   = get_user_meta( $user_id, 'pmpro_scountry', true );
			$phone     = get_user_meta( $user_id, 'pmpro_sphone', true );

			// Build address lines, removing empty values automatically.
			$address_lines = array_filter( array(
				trim( $firstname . ' ' . $lastname ),
				$address1,
				$address2, // If empty, it gets removed — no blank br.
				trim( $city . ', ' . $state . ' ' . $zip ),
				$country,
				$phone,
			) );

			// Convert to <br>-separated output.
			$mailing_address = implode( '<br />', array_map( 'esc_html', $address_lines ) );

			echo wp_kses_post( $mailing_address );
			?>
		</div>
	</div>
	<?php
}
add_action( 'pmpro_after_order_view_main', 'my_pmpro_add_mailing_address_to_admin_view_order_view' );