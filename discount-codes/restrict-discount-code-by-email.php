<?php
/**
 * Restrict discount code usage based on the logged in user's email address
 *
 * title: Restrict discount code usage based on the logged in user's email address
 * layout: snippet
 * collection: discount-codes
 * category: checkout
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
					<textarea name='discount_restrictions' style='width: 30%;' rows='4'><?php echo str_replace( "<br />", "", get_option( 'discount_code_restriction_'.$edit ) ); ?></textarea><br/>
					<small>One email address per line.</small>
				</td>
			</tr>
		</tbody>
	</table>
	<?php

}
add_action( 'pmpro_discount_code_after_settings', 'mypmpro_discount_code_restrict_field', 10, 1 );

function mypmpro_save_discount_code_restrict(){

	if( isset( $_REQUEST['discount_restrictions'] ) ){

		$save_id = intval( $_REQUEST['saveid'] );
		$description = nl2br( $_REQUEST['discount_restrictions'] );

		update_option( 'discount_code_restriction_'.$save_id, $description );

	}

}
add_action( 'admin_init', 'mypmpro_save_discount_code_restrict' );

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

	$discount = new PMPro_Discount_Code( $code );
	
	$restrict = get_option( 'discount_code_restriction_'.$discount->id );	

	$restrictions = strip_tags( $restrict );

	$restrictions = str_replace( "\n", ",", $restrictions );

	if( $restrictions !== "" ){
		$emails = explode( ",", $restrictions );
        
		$user = wp_get_current_user();
    
		if( $user->ID > 0 ){
			if( in_array( $user->data->user_email, $emails ) ){
				$okay = true;
			} else {
                pmpro_setMessage( "This discount code cannot be used with the email address associated with your account.", "pmpro_error" );
				$okay = false;
			}
		} 
            
        return $okay;
        
		
	}

	return $okay;

}
add_filter( 'pmpro_check_discount_code', 'mypmpro_validate_discount_code_use_restrict', 10, 4 );