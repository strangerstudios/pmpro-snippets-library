<?php
/**
 * Display an animated CSS Loader instead of "Processing..." on checkout.
 *
title: Display animated loading (progress) bars on PMPro Checkout when submitting.
layout: snippet
collection: Checkout
category: pmpro_processing_message

 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function pmpro_processing_message_custom_css_loader( $message ) {
	$message = '<div id="pmpro_processing_message-loader">Processing&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="pmpro-processing-message-loader"></span></div>';
	return $message;
}
add_filter( 'pmpro_processing_message', 'pmpro_processing_message_custom_css_loader' );

function pmpro_processing_message_css_for_custom_loader() {
	global $pmpro_pages;

	// Let's only load this on the checkout page.
	if ( ! is_page( $pmpro_pages['checkout'] ) && function_exists( 'pmpro_processing_message_custom_css_loader' ) ) {
		return;
	}

	// Thank you @vineethtrv for the loader https://cssloaders.github.io/
	?>
	<style>
		.pmpro-processing-message-loader {
			display: inline-block;
			position: relative;
			width: 85px;
			height: 30px;
			background-repeat: no-repeat;
			background-image: linear-gradient(#f39c12 30px, transparent 0),
								linear-gradient(#f39c12 30px, transparent 0),
								linear-gradient(#f39c12 30px, transparent 0),
								linear-gradient(#f39c12 30px, transparent 0),
								linear-gradient(#f39c12 30px, transparent 0),
								linear-gradient(#f39c12 30px, transparent 0);
			background-position: 0px center, 15px center, 30px center, 45px center, 60px center, 75px center, 90px center;
			animation: rikSpikeRoll 0.7s linear infinite alternate;
		}
		@keyframes rikSpikeRoll {
			0% { background-size: 10px 3px;}
			16% { background-size: 10px 30px, 10px 3px, 10px 3px, 10px 3px, 10px 3px, 10px 3px}
			33% { background-size: 10px 30px, 10px 30px, 10px 3px, 10px 3px, 10px 3px, 10px 3px}
			50% { background-size: 10px 10px, 10px 30px, 10px 30px, 10px 3px, 10px 3px, 10px 3px}
			66% { background-size: 10px 3px, 10px 10px, 10px 30px, 10px 30px, 10px 3px, 10px 3px}
			83% { background-size: 10px 3px, 10px 3px,  10px 10px, 10px 30px, 10px 30px, 10px 3px}
			100% { background-size: 10px 3px, 10px 3px, 10px 3px,  10px 10px, 10px 30px, 10px 30px}
		}
		#pmpro_processing_message-loader {
			width: 220px;
			display: flex;
			justify-content: center;
			align-items: center;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'pmpro_processing_message_css_for_custom_loader' );
