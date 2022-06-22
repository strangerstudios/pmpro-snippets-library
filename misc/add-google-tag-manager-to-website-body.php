<?php
/**
 * Fallback to add GTM to body of page.
 *
 */
function pmpro_add_google_tag_manager_to_body() {
  // Don't track admins.
	if ( current_user_can( 'manage_options' ) ) {
		return;
	} ?>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-YOURIDHERE"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	<?php
}
add_action( 'memberlite_before_page', 'pmpro_add_google_tag_manager_to_body' );
