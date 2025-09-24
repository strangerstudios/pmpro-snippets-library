<?php
/**
 * This recipe adds the member's shipping address information
 * when viewing an order in the WordPress dashboard.
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

function my_pmpro_add_mailing_address_to_admin_order_view( $order ) {

		$user_id = $order->user_id;

		$firstname = get_user_meta($user_id, "pmpro_sfirstname", true);
		$lastname = get_user_meta($user_id, "pmpro_slastname", true);	
		$address1 = get_user_meta($user_id, "pmpro_saddress1", true);
		$address2 = get_user_meta($user_id, "pmpro_saddress2", true);
		$city = get_user_meta($user_id, "pmpro_scity", true);
		$state = get_user_meta($user_id, "pmpro_sstate", true );
		$zip = get_user_meta($user_id, "pmpro_szipcode", true );		
		$country = get_user_meta($user_id, "pmpro_scountry", true );
?>
		<tr>
		<th><strong>Mailing Address:</strong></th>
		<td><?php echo $firstname . ' ' . $lastname . ', ' . $address1 . ', ' . $address2 . ', ' . $city . ', ' . $state . ', ' . $zip . ', ' . $country; ?></td>
		</tr>
<?php
}
add_action( 'pmpro_after_order_settings', 'my_pmpro_add_mailing_address_to_admin_order_view' );