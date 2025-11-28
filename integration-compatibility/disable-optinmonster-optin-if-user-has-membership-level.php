<?php
/**
 * Disable an OptinMonster optin if user has any membership level.
 * Replace YOUR_OPTIN_SLUG with the slug of the optin you want to remove for members.
 *
 * title: Disable an OptinMonster optin if user has any membership level.
 * layout: snippet
 * collection: integration-compatibility
 * category: content, optinmonster
 * link: https://www.paidmembershipspro.com/membership-logic-enable-disable-popups-popup-maker-popups-optinmonster/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_pmpro_om_remove_optins() {
	if ( function_exists( 'pmpro_hasMembershipLevel' ) && pmpro_hasMembershipLevel() ) {
		echo "<script type='text/javascript'>
			document.addEventListener('om.DisplayRules.afterRun', function(event) {
				var DisplayRules = event.detail.DisplayRules;
				var Campaign = event.detail.Campaign;
				if ( 'YOUR_OPTIN_SLUG' === Campaign.id && DisplayRules.show ) {
					DisplayRules.show = false;
				}
			});
		</script>";
	}
}
add_action( 'wp_footer', 'my_pmpro_om_remove_optins' );
