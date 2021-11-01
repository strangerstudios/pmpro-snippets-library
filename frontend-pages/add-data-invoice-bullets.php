---
layout: snippet
title: Add Data to Invoice Bullets
description: Adds information to the bottom of an invoice in the unordered list.
collection: frontend-pages
category: invoices
---

<?php

/**
 * This recipe will add a Company name to the bottom of the invoice. 
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_invoice_company_name() {

	// Get user meta
	global $current_user;
	
	$company = get_user_meta( $current_user->ID, 'company', true );
	
	// Add if meta is available
	if ( ! empty( $company ) ) {
	?>
	<li><strong>Company:</strong> <?php echo $company; ?></li>
	<?php
	}
}
add_action( 'pmpro_invoice_bullets_top', 'my_pmpro_invoice_company_name' );