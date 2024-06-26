<?php
/**
 * When LPV grants access to a post, show a banner with the number of views remaining.
 *
 * title: Display a countdown banner for the Limit Post Views Add On.
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
 * Filter the JavaScript to run when LPV grants access to a post to show a banner with the number of views remaining.
 *
 * @param string  $notification_js JavaScript to run when LPV grants access to a post.
 * @param int     $views_remaining Number of views remaining.
 */
function my_pmprolpv_allow_view_js_countdown_banner( $notification_js, $views_remaining, $level_views, $level_period ) {
	$f = new NumberFormatter('en', NumberFormatter::SPELLOUT);
	$formatted_views = $f->format($views_remaining);
	$article_text = ($views_remaining == 1) ? 'article' : 'articles';
	$login_url = wp_login_url( get_permalink() );
	$subscribe_url = pmpro_url( 'levels' );

	ob_start();
	?>
	<div style="padding: 40px; 20px;">You have <strong style="background-color: #B00000; border-radius: 5px; display: inline-block; padding: 0 4px;"><?php echo esc_html($formatted_views); ?></strong> free <?php echo esc_html( $article_text );?> remaining this <?php echo esc_html( $level_period ); ?>. <a href="<?php echo esc_url( $login_url ); ?>" title="Log in" style="border-bottom: 1px dotted #CCC; color: #FFF; font-weight: bold; text-decoration: none;">Log in</a> or <a href="<?php echo esc_url( $subscribe_url ); ?>" title="Subscribe now" style="border-bottom: 1px dotted #CCC; color: #FFF; font-weight: bold; text-decoration: none;">Subscribe now</a> for unlimited access.</div>
	<?php
	$banner_content = ob_get_clean();

	return "
		jQuery(document).ready(function($) {
			var banner = $('<div></div>').css({
				'background': 'linear-gradient(to right, rgb( 0, 0, 0 ), rgb( 90, 90, 90 ) )',
				'bottom': '0',
				'box-shadow': '0 1px 10px rgba(0, 0, 0, 0.6)',
				'color': '#FFF',
				'font-size': '20px',
				'left': '0',
				'position': 'fixed',
				'text-align': 'center',
				'width': '100%'
			}).html(" . wp_json_encode( $banner_content ) . ");

			$('body').append(banner);
		});
	";
}
add_filter( 'pmprolpv_allow_view_js', 'my_pmprolpv_allow_view_js_countdown_banner', 10, 4 );
