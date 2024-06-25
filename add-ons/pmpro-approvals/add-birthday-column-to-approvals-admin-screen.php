<?php
/**
 * This recipe adds a "Birthday" column to the Approvals admin screen when using PMPro Approvals.
 *
 * The user field "Birthday" must be added using the Memberships > Settings > User Fields screen.
 *
 * title: Add birthday field to Approvals admin screen
 * layout: snippet
 * collection: add-ons
 * category: pmpro-approvals
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Add a column header for 'birthday' to the Memberships > Approvals admin screen.
 */
function my_pmpro_approvals_list_extra_cols_header( $theuser ) {
	echo '<td>Birthday</td>';
}
add_action( 'pmpro_approvals_list_extra_cols_header', 'my_pmpro_approvals_list_extra_cols_header' );

/**
 * Populate the 'birthday' column with user data.
 */
function my_pmpro_approvals_list_extra_cols_body( $theuser ) {
	echo '<td>' . get_user_meta( $theuser->ID, 'birthday', true ) . '</td>';
}
add_action( 'pmpro_approvals_list_extra_cols_body', 'my_pmpro_approvals_list_extra_cols_body' );
