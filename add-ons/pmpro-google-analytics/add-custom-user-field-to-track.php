<?php
/**
 * Capture User Fields as User Properties For Reporting in Google Analytics Integration Add On
 * title: Capture User Fields as User Properties For Reporting in Google Analytics Integration Add On
 * layout: snippet
 * collection: add-ons, pmpro-google-analytics
 * category: users, custom user fields
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
 
/**
 * Add custom user field to gtag_config_user_properties array to track
 *
 * @param array $gtag_config_user_properties The default user properties.
 * @return array $gtag_config_user_properties customized used properties.
 */
function my_custom_user_field_track( $gtag_config_user_properties ) {
  // Change company_role with the user fields 
	$gtag_config_user_properties['company_role'] = get_user_meta( get_current_user_id(), 'company_role', true );
	return $gtag_config_user_properties;
}

add_filter( 'pmproga4_user_properties', 'my_custom_user_field_track' );