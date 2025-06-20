<?php
/**
 * Use this snippet to set a specific post ID—such as one linking to a PDF or other Media Library file—
 * as the Terms of Service agreement shown at checkout.
 *
 * title: Link Terms of Service Using PDF or Media Library File rather then a page.
 * layout: snippet
 * collection: checkout
 * category: registration-check, tos, terms of service
 * link: tbd
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_replace_tos_field_with_post_id( $settings ) {
	$settings['tospage'] = array(
		'field_name'  => 'tospage',
		'field_type'  => 'text', // replaces callback with basic text input
		'label'       => __( 'Terms of Service Post ID', 'paid-memberships-pro' ),
		'description' => __( 'Enter the ID of the WordPress content to use as the Terms of Service agreement.', 'paid-memberships-pro' ),
	);
	return $settings;
}
add_filter( 'pmpro_custom_advanced_settings', 'my_pmpro_replace_tos_field_with_post_id', 20 );