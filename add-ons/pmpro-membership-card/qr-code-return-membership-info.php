<?php
/*
 * Returns membership information when scanning the QR Code on member's Membership Card.
 * Learn more at https://www.paidmembershipspro.com/membership-qr-code/
 *
 * Set your shortcode to [pmpro_membership_card qr_code='true' qr_data='other'] 
 * for this filter to take effect.
 *
 * title: Return membership information on Membership Card QR Code
 * layout: snippet
 * collection: add-ons, pmpro-membership-card
 * category: qr-code
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function qr_code_return_membership_info( $member, $option ) {
	if ( $option == 'other' ) {
		$return_value = array();
		if ( isset( $member->membership_level ) ) {
			$return_value['first_name'] = $member->first_name;
			$return_value['last_name'] = $member->last_name;
			$return_value['membership_name'] = $member->membership_level->name;
			$return_value['membership_expiration'] = $member->membership_level->enddate;
			$return_value['date_of_query'] = date_i18n( "Y-m-d H:i s" , date( 'y-m-d h:i:s'  ) );
		} else {
			$return_value['membership_name'] = "No membership";
        }
		return json_encode( $return_value, JSON_UNESCAPED_UNICODE );
	}	
}
add_filter( 'pmpro_membership_card_qr_data_other', 'qr_code_return_membership_info', 10, 2 );
