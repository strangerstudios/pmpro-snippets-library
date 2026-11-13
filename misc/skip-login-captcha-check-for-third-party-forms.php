<?php
/**
 * Skip PMPro's login captcha check on login forms that never rendered the captcha.
 *
 * PMPro 3.9 requires a captcha on login POSTs to wp-login.php from IPs with
 * recent failed logins. Third-party login forms post there but never show the
 * widget, so they always fail with "Captcha verification failed." This snippet
 * skips the check when no captcha response field is submitted. Forms that
 * render the widget still submit it and are still checked.
 *
 * Security note: bots can leave the field out too, so this turns off PMPro's
 * login challenge on wp-login.php for them as well, not just for the
 * third-party form.
 *
 * title: Skip PMPro's login captcha check on login forms that never rendered the captcha.
 * layout: snippet
 * collection: misc
 * category: login
 * link: TBD
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_skip_login_captcha_for_third_party_forms() {
	// Only POSTs run PMPro's login checks.
	if ( empty( $_SERVER['REQUEST_METHOD'] ) || 'POST' !== strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) ) {
		return;
	}

	// No captcha enabled, so PMPro added no checks to remove.
	if ( empty( pmpro_captcha() ) ) {
		return;
	}

	// The form submitted a captcha response field, so it did render the widget.
	// Leave PMPro's check in place.
	if ( isset( $_POST['cf-turnstile-response'] ) || isset( $_POST['g-recaptcha-response'] ) ) {
		return;
	}

	// Optional: only skip the check for one page's login form. Uncomment this
	// block and set the path to the page that hosts the third-party form.
	// Note: the referer header is sent by the client and can be faked, so this
	// limits which real visitors skip the check but does not stop bots.
	/*
	$referer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
	if ( false === strpos( $referer, '/login/' ) ) {
		return;
	}
	*/

	remove_filter( 'pmpro_authenticate_login_checks', 'pmpro_recaptcha_login_check', 10 );
	remove_filter( 'pmpro_authenticate_login_checks', 'pmpro_cloudflare_turnstile_login_check', 10 );
}
add_action( 'login_init', 'my_pmpro_skip_login_captcha_for_third_party_forms' );