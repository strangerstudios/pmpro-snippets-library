<?php
/**
 * This ensures that various PMPro pages (Levels, Billing, Cancel, Checkout, Confirmation, and Invoice) render properly.
 *
 * title: Register PMPro pages shortcodes for use within Brick Builder
 * layout: snippet
 * collection: integration-compatibility
 * category: content, bricks builder
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Check if PMPro is active
if ( ! defined('PMPRO_VERSION') ) {
	return;
}

// This registers PMPro shortcodes to be used in Bricks Builder.
$pmpro_shortcodes = [
	'pmpro_levels'      => 'levels',
	'pmpro_billing'     => 'billing',
	'pmpro_cancel'      => 'cancel',
	'pmpro_checkout'    => 'checkout',
	'pmpro_confirmation'=> 'confirmation',
	'pmpro_invoice'     => 'invoice'
];

foreach ($pmpro_shortcodes as $shortcode => $template) {
	if (!shortcode_exists($shortcode)) {
		add_shortcode($shortcode, function() use ($template) {
			require_once PMPRO_DIR . "/preheaders/{$template}.php";
			return pmpro_loadTemplate($template, 'local', 'pages');
		});
	}
}
