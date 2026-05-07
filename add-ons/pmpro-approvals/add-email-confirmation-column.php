<?php
/**
 * This recipe adds an "Email Confirmation" column to the Approvals admin screen when using PMPro Approvals.
 *
 * The recipe assumes the Email Confirmation Add On is active.
 *
 * title: Add Email Confirmation status to Approvals admin screen.
 * layout: snippet
 * collection: add-ons
 * category: pmpro-approvals, pmpro-email-confirmation
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Add column header
function my_pmpro_approvals_list_extra_cols_header_email_confirmation( $the_user ) {
	?>
	<th><?php esc_html_e( 'Email Confirmation', 'pmpro-snippets-library' ); ?></th>
	<?php
}
add_action( 'pmpro_approvals_list_extra_cols_header', 'my_pmpro_approvals_list_extra_cols_header_email_confirmation' );

// Add column body
function my_pmpro_approvals_list_extra_cols_body_email_confirmation( $the_user ) {
	?>
	<td>
	<?php
	$email_confirmation_key = get_user_meta( $the_user->ID, 'pmpro_email_confirmation_key', true );
	if ( empty( $email_confirmation_key ) ) {
		esc_html_e( 'Not requested', 'pmpro-snippets-library' );
	} elseif ( 'validated' == $email_confirmation_key ) {
		esc_html_e( 'Validated', 'pmpro-snippets-library' );
	} else {
		esc_html_e( 'Not validated', 'pmpro-snippets-library' );
	}
	?>
	</td>
	<?php
}
add_action( 'pmpro_approvals_list_extra_cols_body', 'my_pmpro_approvals_list_extra_cols_body_email_confirmation' );
