<?php
/**
 * When LPV grants access to a post, show a banner with the number of views remaining.
 *
 * title: Display a countdown banner for the Limit Post Views Add On.
 * layout:snippet
 * category: limit post views, banner
 * link: https://www.paidmembershipspro.com/improve-the-user-experience-and-increase-signups-when-using-the-limit-post-views-add-on/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Filter the JavaScript to run when LPV grants access to a post to show a banner with the number of views remaining.
 *
 * @param string  $notification_js JavaScript to run when LPV grants access to a post.
 * @param int     $views_remaining Number of views remaining.
 */
function my_pmprolpv_allow_view_js_countdown_banner( $notification_js, $views_remaining ) {
	$f = new NumberFormatter('en', NumberFormatter::SPELLOUT);
	$formatted_views = $f->format($views_remaining);
	$article_text = ($views_remaining == 1) ? 'article' : 'articles';
	$login_url = wp_login_url( get_permalink() );
	$subscribe_url = pmpro_url( 'levels' );

	return "
		jQuery(document).ready(function($) {
			var banner = $('<div></div>').css({
				'background': '#d4f1df',
				'bottom': '0',
				'font-size': '2.5rem',
				'left': '0',
				'padding': '1em',
				'position': 'fixed',
				'width': '100%'
			}).html('You have <span style=\"color: #B00000;\">{$formatted_views}</span> free {$article_text} remaining. <a href=\"{$login_url}\" title=\"Log in\">Log in</a> or <a href=\"{$subscribe_url}\" title=\"Subscribe now\">Subscribe</a> now for unlimited online access.');

			$('body').append(banner);
		});
	";
}
add_filter( 'pmprolpv_allow_view_js', 'my_pmprolpv_allow_view_js_countdown_banner', 10, 2 );
