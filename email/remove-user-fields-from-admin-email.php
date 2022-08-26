<?php
/**
 * Remove user fields automatically added to admin checkout emails.
 * 
 * title: Remove user fields automatically added to admin checkout emails.
 * collection: email
 * category: custom-fields
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_remove_user_fields_from_admin_emails() {
	remove_filter( 'pmpro_email_filter', 'pmpro_add_user_fields_to_email', 10, 2);	
}
add_action( 'init', 'my_pmpro_remove_user_fields_from_admin_emails', 11 );
