<?php
/**
 * Creates shipping address fields for 'child' accounts when using the Sponsored/Group Members for Paid Memberships Pro.
 * This requires the following attribute 'sponsored_accounts_at_checkout' => true for checkout.
 * PLEASE NOTE: Using the Register Helper Add On (or similar) may interfere with this code if field names match.
 *
 * title: Create shipping address fields for "child" account
 * layout: snippet
 * collection: add-ons, sponsored/group members
 * category: shipping, child account
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 function pmprosm_additional_child_fields( $i, $seats ) {
     ?>
         <label>Shipping Address 1</label><input type="text" name="add_child_saddress1[]" value="" size="20" />
         <label>Shipping Address 2</label><input type="text" name="add_child_saddress2[]" value="" size="20" />
         <label>City</label><input type="text" name="add_child_scity[]" value="" size="20" />
         <label>State</label><input type="text" name="add_child_sstate[]" value="" size="20" />
         <label>Postal Code</label><input type="text" name="add_child_szipcode[]" value="" size="20" />
     <?php
 }
 add_action( 'pmprosm_children_fields', 'pmprosm_additional_child_fields', 10, 2 );

 function pmprosm_save_additional_child_fields( $child_user_id, $user_id, $i ) {

     if ( ! empty( $_REQUEST['add_sub_accounts_first_name'] ) ) {
         $child_first_name = sanitize_text_field( $_REQUEST['add_sub_accounts_first_name'][$i] );
     } else {
         $child_first_name = '';
     }

     if ( ! empty( $_REQUEST['add_sub_accounts_last_name'] ) ) {
         $child_last_name = sanitize_text_field( $_REQUEST['add_sub_accounts_last_name'][$i] );
     } else {
         $child_last_name = '';
     }

     if ( ! empty( $_REQUEST['add_child_saddress1'] ) ) {
         $child_saddress1 = sanitize_text_field( $_REQUEST['add_child_saddress1'][$i] );
     } else {
         $child_saddress1 = '';
     }

     if ( ! empty( $_REQUEST['add_child_saddress2'] ) ) {
         $child_saddress2 = sanitize_text_field( $_REQUEST['add_child_saddress2'][$i] );
     } else {
         $child_saddress2 = '';
     }

     if ( ! empty( $_REQUEST['add_child_scity'] ) ) {
         $child_scity = sanitize_text_field( $_REQUEST['add_child_scity'][$i] );
     } else {
         $child_scity = '';
     }

     if ( ! empty( $_REQUEST['add_child_sstate'] ) ) {
         $child_sstate = sanitize_text_field( $_REQUEST['add_child_sstate'][$i] );
     } else {
         $child_sstate = '';
     }

     if ( ! empty( $_REQUEST['add_child_szipcode'] ) ) {
         $child_szipcode	= sanitize_text_field( $_REQUEST['add_child_szipcode'][$i] );
     } else {
         $child_szipcode = '';
 	}

     if ( ! empty( $child_saddress1 ) ) {
         update_user_meta( $child_user_id, "pmpro_sfirstname", $child_first_name );
         update_user_meta( $child_user_id, "pmpro_slastname", $child_last_name );
         update_user_meta( $child_user_id, "pmpro_saddress1", $child_saddress1 );
         update_user_meta( $child_user_id, "pmpro_saddress2", $child_saddress2 );
         update_user_meta( $child_user_id, "pmpro_scity", $child_scity );
         update_user_meta( $child_user_id, "pmpro_sstate", $child_sstate );
         update_user_meta( $child_user_id, "pmpro_szipcode", $child_szipcode );
     }
 }
 add_action( 'pmprosm_after_child_created', 'pmprosm_save_additional_child_fields', 10, 3 );
