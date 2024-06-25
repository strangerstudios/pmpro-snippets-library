<?php
/**
 * This recipe will add a Description field to each discount code. This is visible on the
 * Discount Codes page for admin reference.
 *
 * title: Add description to discount codes
 * layout: snippet
 * collection: discount-codes
 * category: custom-fields, description
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function mypmpro_discount_code_notes_field( $edit ) {

	if ( $_REQUEST['edit'] !== '-1' ) {

		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top">
						<label for="uses">Description</label>
					</th>
					<td>
						<textarea name='discount_description' style='width: 30%;' rows='4'><?php echo str_replace( '<br />', '', get_option( 'discount_code_description_' . $edit ) ); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	} else {
		echo 'You will need to save your discount code before you can enter a description';
	}

}
add_action( 'pmpro_discount_code_after_settings', 'mypmpro_discount_code_notes_field', 10, 1 );

function mypmpro_save_discount_code_note() {

	if ( isset( $_REQUEST['discount_description'] ) ) {

		$save_id     = intval( $_REQUEST['saveid'] );
		$description = nl2br( $_REQUEST['discount_description'] );

		update_option( 'discount_code_description_' . $save_id, $description );

	}

}
add_action( 'admin_init', 'mypmpro_save_discount_code_note' );

function mypmpro_discount_page_header( $columns ) {
 
	$columns['discount_description'] = 'Description';

	return $columns;
 
}
add_filter( 'pmpro_manage_discountcodes_columns', 'mypmpro_discount_page_header', 10, 1 );
 
function mypmpro_discount_page_column( $column_name, $code_id ) {

	if( $column_name == 'discount_description' ) {

		echo get_option( 'discount_code_description_' . $code_id );

	}
 
}
add_action( 'pmpro_manage_discount_code_list_custom_column', 'mypmpro_discount_page_column', 10, 2 );
