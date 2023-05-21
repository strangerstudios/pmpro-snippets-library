<?php
/**
 * Change Membership Level on Expiration Only
 * Users who manually cancel are cancelled immediately, but users who expire are given a grace period.)
 *
 * SET level to be assigned on line 34
 * 
 * title: Change Membership Level on Expiration Only
 * layout: snippet
 * collection: membership-levels
 * category: expiration
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 function pmpro_after_change_membership_level_default_level($level_id, $user_id)
{
	//if we see this global set, then another gist is planning to give the user their level back
	global $pmpro_next_payment_timestamp, $wpdb;
	if(!empty($pmpro_next_payment_timestamp))
		return;
	
	if($level_id == 0) {
		
		$levels_history = $wpdb->get_results("SELECT * FROM $wpdb->pmpro_memberships_users WHERE user_id = '$user_id' ORDER BY id DESC");
		
		$last_level = $levels_history[0];

		//expiring, give them level 1 instead
		if($last_level->status == 'expired')
			pmpro_changeMembershipLevel(1, $user_id);	//change to your desired level
	}
}
add_action("pmpro_after_change_membership_level", "pmpro_after_change_membership_level_default_level", 10, 2);