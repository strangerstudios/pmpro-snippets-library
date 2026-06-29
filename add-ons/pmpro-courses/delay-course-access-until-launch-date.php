<?php
/**
 * Delay Course and Lesson Access Until a Launch Date with Countdown for Members
 * Blocks access to PMPro Courses Add On course and lesson content until a set launch
 * date. Members who have purchased but cannot yet access will see a countdown card
 * above the default restricted content message.
 *
 * title: Delay Course and Lesson Access Until a Launch Date with Countdown for Members
 * layout: snippet
 * collection: add-ons/pmpro-courses
 * category: access
 * link: tbd
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Block access to course and lesson content until the launch date.
 * Update $launch_date to your course launch date.
 * Remove this snippet once the course has launched.
 */
function my_pmpro_delay_course_access_until_launch( $hasaccess, $post, $user, $levels ) {
	// Set your course launch date (YYYY-MM-DD).
	$launch_date = '2026-07-01';

	// Only act before the launch date.
	if ( current_time( 'Y-m-d' ) >= $launch_date ) {
		return $hasaccess;
	}

	// Block access to course pages and individual lessons only.
	if ( in_array( get_post_type( $post ), array( 'pmpro_course', 'pmpro_lesson' ), true ) ) {
		return false;
	}

	return $hasaccess;
}
add_filter( 'pmpro_has_membership_access_filter', 'my_pmpro_delay_course_access_until_launch', 10, 4 );

/**
 * Show a countdown card above the restricted content message for members who have
 * purchased but cannot access the course yet due to the launch date.
 * Update $launch_date to match the date set in the access filter above.
 * Remove this snippet once the course has launched.
 */
function my_pmpro_course_launch_countdown_content( $content ) {
	// Set your course launch date (YYYY-MM-DD) — must match the date in the filter above.
	$launch_date = '2026-07-01';

	// Only act before the launch date.
	if ( current_time( 'Y-m-d' ) >= $launch_date ) {
		return $content;
	}

	// Only on course or lesson post types.
	if ( ! is_singular( array( 'pmpro_course', 'pmpro_lesson' ) ) ) {
		return $content;
	}

	// Only for logged-in members with a qualifying membership level.
	if ( ! is_user_logged_in() || ! pmpro_hasMembershipLevel() ) {
		return $content;
	}

	$launch_timestamp = $launch_date . 'T00:00:00';
	$launch_formatted = date_i18n( get_option( 'date_format' ), strtotime( $launch_date ) );

	ob_start();
	?>
	<div style="border:1px solid #e0e0e0;border-radius:8px;padding:24px 28px;margin-bottom:24px;background:#f9f9f9;text-align:center;font-family:inherit;">
		<p style="margin:0 0 6px;font-size:1.1em;font-weight:600;">🎉 You're registered! This course launches on <?php echo esc_html( $launch_formatted ); ?>.</p>
		<p style="margin:0 0 20px;color:#555;font-size:0.95em;">Hang tight — full access unlocks automatically on launch day.</p>
		<div id="pmpro-countdown-timer" style="display:flex;justify-content:center;gap:16px;">
			<div><span id="pmpro-cd-days" style="display:block;font-size:2em;font-weight:700;">--</span><span style="font-size:0.8em;color:#777;">Days</span></div>
			<div style="font-size:2em;font-weight:700;line-height:1.4;">:</div>
			<div><span id="pmpro-cd-hours" style="display:block;font-size:2em;font-weight:700;">--</span><span style="font-size:0.8em;color:#777;">Hours</span></div>
			<div style="font-size:2em;font-weight:700;line-height:1.4;">:</div>
			<div><span id="pmpro-cd-minutes" style="display:block;font-size:2em;font-weight:700;">--</span><span style="font-size:0.8em;color:#777;">Minutes</span></div>
			<div style="font-size:2em;font-weight:700;line-height:1.4;">:</div>
			<div><span id="pmpro-cd-seconds" style="display:block;font-size:2em;font-weight:700;">--</span><span style="font-size:0.8em;color:#777;">Seconds</span></div>
		</div>
	</div>
	<script>
	(function() {
		var launchDate = new Date( '<?php echo esc_js( $launch_timestamp ); ?>' );
		function updateCountdown() {
			var diff = launchDate - new Date();
			if ( diff <= 0 ) {
				document.getElementById( 'pmpro-countdown-timer' ).style.display = 'none';
				return;
			}
			document.getElementById( 'pmpro-cd-days' ).textContent    = String( Math.floor( diff / 86400000 ) ).padStart( 2, '0' );
			document.getElementById( 'pmpro-cd-hours' ).textContent   = String( Math.floor( ( diff % 86400000 ) / 3600000 ) ).padStart( 2, '0' );
			document.getElementById( 'pmpro-cd-minutes' ).textContent = String( Math.floor( ( diff % 3600000 ) / 60000 ) ).padStart( 2, '0' );
			document.getElementById( 'pmpro-cd-seconds' ).textContent = String( Math.floor( ( diff % 60000 ) / 1000 ) ).padStart( 2, '0' );
		}
		updateCountdown();
		setInterval( updateCountdown, 1000 );
	})();
	</script>
	<?php
	$countdown = ob_get_clean();

	return $countdown . $content;
}
add_filter( 'the_content', 'my_pmpro_course_launch_countdown_content', 20 );