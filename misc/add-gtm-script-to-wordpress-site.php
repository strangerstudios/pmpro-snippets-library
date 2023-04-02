<?php
/** 
 * Load GTM tracking snippet in the head of your WordPress site.
 *
 * title: Load GTM tracking snippet in the head of your WordPress site.
 * layout: snippet
 * collection: misc
 * category: analytics
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_add_google_tag_manager_to_head() {
	// Don't track admins.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	} ?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-1234567');</script>
	<!-- End Google Tag Manager -->
	<?php
}
add_action( 'wp_head', 'my_pmpro_add_google_tag_manager_to_head' );

/**
 * Fallback to add GTM to body of page (optional).
 * 
 */
function my_pmpro_add_google_tag_manager_to_body() {
	// Don't track admins.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	} ?>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-1234567"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	<?php
}
//add_action( 'wp_body_open', 'my_pmpro_add_google_tag_manager_to_body' );
