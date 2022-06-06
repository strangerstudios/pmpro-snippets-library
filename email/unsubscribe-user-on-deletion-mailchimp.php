<?php
/**
 * This recipe allow unsubscribe a member from Mailchimp when deleting their user account in WP.
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function mypmpro_unsubscribe_on_user_deletion( $user_id ){

	if( function_exists( 'pmpromc_queue_unsubscription' ) ){

		$user = get_userdata( $user_id );

		$audiences = get_user_meta( $user_id, 'pmpromc_additional_lists', true );

		if ( ! empty( $audiences ) ) {
			pmpromc_queue_unsubscription( $user, $audiences );
		}

	}

}
add_action( 'delete_user', 'mypmpro_unsubscribe_on_user_deletion', 10, 1 );
