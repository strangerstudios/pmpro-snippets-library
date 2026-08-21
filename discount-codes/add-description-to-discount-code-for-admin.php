<?php
/**
 * This recipe will add a Description field to each discount code. This is visible on the
 * Discount Codes page for admin reference.
 *
 * title: Add description to discount codes
 * layout: snippet
 * collection: discount-codes
 * category: custom-fields, description
 * link: https://www.paidmembershipspro.com/discount-code-description-field/
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Adds a Description field to the discount code edit screen.
 *
 * @param int $edit The ID of the discount code being edited, or -1 if unsaved.
 */
function my_pmpro_discount_code_description_field( $edit ) {
	if ( ! isset( $_REQUEST['edit'] ) || '-1' === $_REQUEST['edit'] ) {
		?>
		<div class="pmpro_message pmpro_alert">You must save this discount code before you can enter a description.</div>
		<?php
		return;
	}

	$description = get_option( 'discount_code_description_' . $edit );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row" valign="top">
					<label for="discount_description">Description</label>
				</th>
				<td>
					<textarea id="discount_description" name="discount_description" style="width: 30%;" rows="4"><?php echo esc_textarea( $description ); ?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}
add_action( 'pmpro_discount_code_after_settings', 'my_pmpro_discount_code_description_field', 10, 1 );

/**
 * Saves the Description field when the discount code screen is submitted.
 */
function my_pmpro_save_discount_code_description() {
	if ( ! isset( $_REQUEST['discount_description'], $_REQUEST['saveid'] ) ) {
		return;
	}
	$save_id     = intval( $_REQUEST['saveid'] );
	$description = sanitize_textarea_field( wp_unslash( $_REQUEST['discount_description'] ) );
	update_option( 'discount_code_description_' . $save_id, $description );
}
add_action( 'admin_init', 'my_pmpro_save_discount_code_description' );

/**
 * Adds a Description column to the Discount Codes list.
 *
 * @param array $columns Table column headers.
 * @return array
 */
function my_pmpro_discount_code_description_column( $columns ) {
	$columns['discount_description'] = 'Description';
	return $columns;
}
add_filter( 'pmpro_manage_discountcodes_columns', 'my_pmpro_discount_code_description_column' );

/**
 * Fills the Description column on the Discount Codes list.
 *
 * @param string $column_name The current column name.
 * @param int    $code_id     The discount code ID for this row.
 */
function my_pmpro_discount_code_description_column_content( $column_name, $code_id ) {
	if ( 'discount_description' === $column_name ) {
		echo esc_html( get_option( 'discount_code_description_' . $code_id ) );
	}
}
add_action( 'pmpro_manage_discount_code_list_custom_column', 'my_pmpro_discount_code_description_column_content', 10, 2 );