<?php
/**
 * When LPV grants access to a post, show a banner with the number of views used.
 *
 * title: Display a banner with the number of views used for the Limit Post Views Add On.
 * layout: snippet
 * category: limit post views, banner
 * link: https://www.paidmembershipspro.com/improve-the-user-experience-and-increase-signups-when-using-the-limit-post-views-add-on/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Filter the JavaScript to run when LPV grants access to a post to show a banner with the current and total views.
 *
 * @param string  $notification_js JavaScript to run when LPV grants access to a post.
 * @param int     $views_remaining Number of views remaining.
 * @param int     $level_views     Total views allowed for the user's level.
 */
function my_pmprolpv_allow_view_js_views_used_banner( $notification_js, $views_remaining, $level_views ) {
	$views_used = $level_views - $views_remaining;
	$login_url = esc_url( wp_login_url( get_permalink() ) );
	$subscribe_url = esc_url( pmpro_url( 'levels' ) );

	return "
		jQuery(document).ready(function($) {
			var banner = $('<div></div>').addClass('banner banner_action').css({
				'background': 'linear-gradient(to right, rgb( 0, 0, 0 ), rgb( 90, 90, 90 ) )',
				'bottom': '0',
				'box-shadow': '0 1px 10px rgba(0, 0, 0, 0.6)',
				'color': '#FFF',
				'font-size': '20px',
				'left': '0',
				'width': '100%',
				'position': 'fixed',
			});

			var viewsInfo = $('<div></div>').css({
				'display': 'inline-block',
				'font-weight': '700',
				'margin-right': '40px',
				'padding': '40px',
				'background': '#B00000',
			}).text('{$views_used}/{$level_views}');

			var loginLink = $('<a></a>').attr({
				'href': '{$login_url}',
				'title': 'Log in'
			}).css({
				'border-bottom': '1px dotted #CCC',
				'color': '#FFF',
				'font-weight': '700',
				'text-decoration': 'none',
			}).text('Log in');

			var subscribeLink = $('<a></a>').attr({
				'href': '{$subscribe_url}',
				'title': 'Subscribe now'
			}).css({
				'border-bottom': '1px dotted #CCC',
				'color': '#FFF',
				'font-weight': '700',
				'text-decoration': 'none',
			}).text('Subscribe now');

			banner.append(viewsInfo).append(loginLink).append(' or ').append(subscribeLink).append(' for unlimited online access.');
			$('body').append(banner);
		});
	";
}
add_filter('pmprolpv_allow_view_js', 'my_pmprolpv_allow_view_js_views_used_banner', 10, 3);
