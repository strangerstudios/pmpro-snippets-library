<?php
/**
 * Skip PMPro's login and lost password captcha checks on forms that never rendered the captcha.
 *
 * Requires PMPro 3.9+.
 *
 * PMPro 3.9 requires a captcha on login and lost password POSTs to wp-login.php
 * from IPs with recent failed logins. Third-party forms that post there but
 * never show the widget always fail with "Captcha verification failed." This
 * snippet skips those checks when no captcha response field is submitted.
 * Forms that render the widget still submit it and are still checked.
 *
 * Try this first: forms built with wp_login_form(), or that run
 * do_action( 'login_form' ) / do_action( 'lostpassword_form' ), already show
 * PMPro's captcha and don't need this snippet.
 *
 * Security note: bots that post straight to wp-login.php don't send the field
 * either, so this also turns off PMPro's login and lost password captcha for
 * them. Use the optional referer check below to limit the bypass to your
 * form's page.
 *
 * title: Skip PMPro's login and lost password captcha checks on forms that never rendered the captcha.
 * layout: snippet
 * collection: misc
 * category: security, login
 * link: TBD
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

function my_pmpro_skip_login_captcha_for_third_party_forms() {
	// Bail if PMPro is inactive or older than 3.9, or no captcha is enabled.
	if ( ! function_exists( 'pmpro_captcha' ) || empty( pmpro_captcha() ) ) {
		return;
	}

	// The form submitted a captcha response field, so it did render the widget.
	// Leave PMPro's checks in place.
	if ( isset( $_REQUEST['cf-turnstile-response'] ) || isset( $_REQUEST['g-recaptcha-response'] ) ) {
		return;
	}

	// Optional: only skip the checks for forms on specific pages. Uncomment this
	// block and set the paths of the pages that host your login and lost
	// password forms. The referer header is sent by the client, so this filters
	// out generic bots but not one that fakes the header.
	/*
	$referer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
	$paths   = array( '/login/', '/forgot-password/' );
	$matched = false;
	foreach ( $paths as $path ) {
		if ( false !== strpos( $referer, $path ) ) {
			$matched = true;
			break;
		}
	}
	if ( ! $matched ) {
		return;
	}
	*/

	// Login checks.
	remove_filter( 'pmpro_authenticate_login_checks', 'pmpro_recaptcha_login_check', 10 );
	remove_filter( 'pmpro_authenticate_login_checks', 'pmpro_cloudflare_turnstile_login_check', 10 );

	// Lost password checks (the "email me a reset link" step).
	remove_action( 'lostpassword_post', 'pmpro_recaptcha_lostpassword_check', 10 );
	remove_action( 'lostpassword_post', 'pmpro_cloudflare_turnstile_lostpassword_check', 10 );
}
add_action( 'login_init', 'my_pmpro_skip_login_captcha_for_third_party_forms' );
