<?php
/**
 * Override Membership Card Template Path
 *
 * This recipe overrides the default Membership Card template path to use a custom template
 * stored in your plugin or customizations directory.
 *
 * title: Override Membership Card Template Path
 * layout: snippet
 * collection: add-ons, pmpro-membership-card
 * category: template-overrides
 * link: https://www.paidmembershipspro.com/customize-membership-card-wordpress/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_override_member_card_template( $template_path, $atts, $content, $code ){
	
	$template_path = plugin_dir_path(__FILE__) . "membership-card.php";

	return $template_path;

}
add_filter( 'pmpro_membership_card_template_path', 'my_pmpro_override_member_card_template', 10, 4 );