<?php
/**
 * Display a banner and blur the page when post access is denied for the Limit Post Views Add On.
 *
 * title: Display a banner and blur the page when post access is denied for the Limit Post Views Add On.
 * layout: snippet
 * category: limit post views, banner
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Filter the JavaScript to run when LPV denies access to a post to show a banner and signup links.
 *
 * @param int    $level_views    Number of views allowed for the user's level.
 * @param string $level_period   Period for the user's level.
 */
function my_pmprolpv_deny_view_js_signup_banner( $restriction_js, $level_views, $level_period ) {
	$login_url = wp_login_url( get_permalink() );
	$subscribe_url = pmpro_url( 'levels' );

	ob_start();
	?>
	<div style="padding: 40px; 20px;">
		<p style="margin-left: auto; margin-right: auto; max-width: 800px;">You have used all your free views this <?php echo esc_html( $level_period );?>. Become a member today to get unlimited access to this post and hundreds more.</p>
		<p style="margin-top: 25px;"><a href="<?php echo esc_url( $subscribe_url );?>" title="Subscribe now" style="background-color: #B00000; border-radius: 10px; color: #FFF; display: inline-block; font-size: 24px; font-weight: bold; padding: 10px 20px; text-decoration: none;">Subscribe Now</a></p>
		<p style="font-size: 16px;">Already a member? <a href="<?php echo esc_url( $login_url ); ?>" title="Log in" style="border-bottom: 1px dotted #CCC; color: #FFF; font-weight: bold; text-decoration: none;">Log in here</a>.</p>
	</div>
	<?php
	$banner_content = ob_get_clean();

	return "
		jQuery(document).ready(function($) {
			var blurLayer = $('<div></div>').css({
				'background': 'rgba(255, 255, 255, 0.8)',
				'backdrop-filter': 'blur(5px)',
				'bottom': '0',
				'left': '0',
				'position': 'fixed',
				'right': '0',
				'top': '80px',
				'width': '100%',
				'z-index': '9998'
			});
			var banner = $('<div></div>').css({
				'background': 'linear-gradient(to right, rgb( 0, 0, 0 ), rgb( 90, 90, 90 ) )',
				'box-shadow': '0 1px 10px rgba(0, 0, 0, 0.6)',
				'color': '#FFF',
				'font-size': '20px',
				'left': '0',
				'min-height': '50vh',
				'position': 'fixed',
				'text-align': 'center',
				'top': '100vh',
		        'transform': 'translate(0px, -50vh)',
				'width': '100%',
				'z-index': '9999'
			}).html(" . wp_json_encode( $banner_content ) . ");

			$('body').append(blurLayer).append(banner);
			$('body').css({
				'height': '100%',
				'overflow': 'hidden',
			});
		});
	";
}
add_filter( 'pmprolpv_deny_view_js', 'my_pmprolpv_deny_view_js_signup_banner', 10, 3 );
