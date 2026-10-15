<?php
/**
 * Restrict discount code usage based on the logged in user's email address (or the email entered at checkout for guests)
 *
 * Guests are checked against the email they enter on the checkout form, so the "Apply" button
 * accepts the code and the rejection shows when they submit checkout. Anyone who knows a listed
 * email can enter it, so this limits who a code is meant for; it does not stop code sharing.
 *
 * title: Restrict discount code usage by email address
 * layout: snippet
 * collection: discount-codes
 * category: checkout
 * link: TBD
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function mypmpro_discount_code_restrict_field( $edit ){

	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row" valign="top">
					<label for="uses">Restrict By Email</label>
				</th>
				<td>
					<textarea name='discount_restrictions' style='width: 30%;' rows='4'><?php echo esc_textarea( str_replace( "<br />", "", get_option( 'discount_code_restriction_'.$edit ) ) ); ?></textarea><br/>
					<small>One email address per line.</small>
				</td>
			</tr>
		</tbody>
	</table>
	<?php

}
add_action( 'pmpro_discount_code_after_settings', 'mypmpro_discount_code_restrict_field', 10, 1 );

function mypmpro_save_discount_code_restrict( $edit ){
	
	// Run it on save.
	if ( isset( $_REQUEST['discount_restrictions'] ) ){

		$description = nl2br( sanitize_textarea_field( wp_unslash( $_REQUEST['discount_restrictions'] ) ) );
		update_option( 'discount_code_restriction_'.$edit, $description );

	}
}
add_action( 'pmpro_save_discount_code', 'mypmpro_save_discount_code_restrict', 10, 1 );

function mypmpro_discount_page_header_restrict( $codes ){

	echo "<th>Has Restrictions?</th>";

}
add_action( 'pmpro_discountcodes_extra_cols_header', 'mypmpro_discount_page_header_restrict', 10, 1 );

function mypmpro_discount_page_column_restrict( $code ){

	if( ! empty( get_option( 'discount_code_restriction_'.$code->id ) ) ){
		echo "<td>Yes</td>";
	} else {
		echo "<td>No</td>";
	}

}
add_action( 'pmpro_discountcodes_extra_cols_body', 'mypmpro_discount_page_column_restrict', 10, 1 );

function mypmpro_validate_discount_code_use_restrict( $okay, $dbcode, $level_id, $code ){

	// Bail if core already rejected the code or did not find a code row.
	if ( ! $okay || empty( $dbcode ) || empty( $dbcode->id ) ) {
		return $okay;
	}

	$restrict = get_option( 'discount_code_restriction_' . $dbcode->id );

	$restrictions = strip_tags( $restrict );

	$restrictions = str_replace( "\n", ",", $restrictions );

	if ( $restrictions === "" ) {
		return $okay;
	}

	$emails = array_filter( array_map( 'trim', explode( ",", $restrictions ) ) );
	$emails = array_map( 'strtolower', $emails );

	// Use the logged in user's email, or the email submitted on the checkout form for guests.
	$user = wp_get_current_user();
	if ( $user->ID > 0 ) {
		$email = $user->user_email;
	} elseif ( ! empty( $_REQUEST['bemail'] ) ) {
		$email = sanitize_email( wp_unslash( $_REQUEST['bemail'] ) );
	} else {
		// No email to check yet (e.g. code applied before the form is submitted). Checked again on submit.
		return $okay;
	}

	// Returning a string tells core to show it as the error message.
	if ( ! in_array( strtolower( $email ), $emails, true ) ) {
		return "This discount code cannot be used with this email address.";
	}

	return $okay;

}
add_filter( 'pmpro_check_discount_code', 'mypmpro_validate_discount_code_use_restrict', 10, 4 );
