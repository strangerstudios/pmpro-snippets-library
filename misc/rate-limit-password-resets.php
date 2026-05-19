<?php
/**
 * Rate limit password reset requests by IP address.
 *
 * title: Rate limit password reset requests
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
function my_rate_limit_password_resets( $errors ) {
	/**
	 * Identify the visitor by IP. REMOTE_ADDR is correct when WordPress is
	 * reached directly. If your site sits behind Cloudflare or another reverse
	 * proxy, REMOTE_ADDR will be the proxy's IP and every visitor will share a
	 * single rate-limit bucket — three failed resets will block resets site-wide
	 * for an hour. In that case swap in the appropriate forwarded-IP header:
	 *
	 *   Cloudflare:    $_SERVER['HTTP_CF_CONNECTING_IP']
	 *   Generic proxy: $_SERVER['HTTP_X_FORWARDED_FOR'] (validate and take the first IP)
	 *
	 * Only trust these headers when you actually have a proxy in front of the
	 * site; on a direct-hit site they can be spoofed by the client.
	 * 
	 * If you are using the latest version of PMPro, you may use `pmpro_get_ip()` instead, which handles proxy headers for you.
	 */
	$ip    = $_SERVER['REMOTE_ADDR'];
	$key   = 'my_pwreset_' . md5( $ip );
	$count = (int) get_transient( $key );

	/**
	 * Cap password reset requests at 3 per IP address per hour. This helps
	 * slow down automated abuse of the lost-password form without affecting
	 * legitimate users who occasionally need a reset.
	 *
	 * Adjust the limit or window by changing the 3 and HOUR_IN_SECONDS values below.
	 *
	 * Note: this is best-effort throttling. Two concurrent requests can both read
	 * the same transient value before either writes, letting a few extra attempts
	 * through under load. For hard enforcement, pair this with server-level rate
	 * limiting (Nginx limit_req, Cloudflare rate rules, etc.).
	 */
	if ( $count >= 3 ) {
		$errors->add( 'too_many_requests', 'Too many password reset requests. Please try again later.' );
		return;
	}

	set_transient( $key, $count + 1, HOUR_IN_SECONDS );
}
add_action( 'lostpassword_post', 'my_rate_limit_password_resets' );
