<?php
/**
 * Process Membership Checkout in a Modal or Popover Window
 *
 * title: Process Membership Checkout in a Modal or Popover Window.
 * layout: snippet
 * collection: checkout
 * category: membership-levels
 * link: https://www.paidmembershipspro.com/process-membership-checkout-in-a-modal-or-popover-window/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Define the default level to use for the modal.
 */
define( 'PMPRO_MODAL_CHECKOUT_DEFAULT_LEVEL', 1 );

/**
 * Load the checkout preheader on every page
 * We won't be processing on every page, but we
 * want the code that sets up the level and
 * preloads fields from user meta.
 */
function pmprocm_preheader() {
	// Bail if PMPro is not loaded.
	if ( ! defined( 'PMPRO_DIR' ) ) {
		return;
	}

	if ( ! is_admin() ) {
		if ( empty( $_REQUEST['pmpro_level'] ) ) {
			$_REQUEST['pmpro_level'] = PMPRO_MODAL_CHECKOUT_DEFAULT_LEVEL;
		}
		require_once PMPRO_DIR . '/preheaders/checkout.php';
	}
}
add_action( 'init', 'pmprocm_preheader' );

/**
 * Add the checkout page modal to every page.
 */
function pmprocm_content() {
	// If the PMPro checkout shortcode or block is present on a post/page, skip modal.
	$queried_object = get_queried_object();
	if ( empty( $queried_object ) ||
		empty( $queried_object->post_content ) ||
		has_shortcode( $queried_object->post_content, 'pmpro_checkout' ) ||
		has_block( 'pmpro/checkout-page', $queried_object->post_content )
	) {
		return;
	}
	?>
	<style>
		div.pmprocm_modal_bg {
			display: none; /* Hidden by default */
			position: fixed; /* Stay in place */
			z-index: 1; /* Sit on top */
			left: 0;
			top: 0;
			width: 100%; /* Full width */
			height: 100%; /* Full height */
			overflow: auto; /* Enable scroll if needed */
			background-color: rgb(0,0,0); /* Fallback color */
			background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
		}

		.pmprocm_modal_content {
			background-color: #fefefe;
			margin: 5% 25% auto; /* 15% from the top and centered */
			padding: 40px;
			border: 1px solid #888;
			width: 50%; /* Could be more or less, depending on screen size */
		}

		.pmprocm_modal_close {
			color: #aaa;
			float: right;
			font-size: 28px;
			font-weight: bold;
			margin: -35px -30px 0 0;
		}

		.pmprocm_modal_close:hover,
		.pmprocm_modal_close:focus {
			color: black;
			text-decoration: none;
			cursor: pointer;
		}
	</style>
	
	<div class="pmprocm_modal_bg">
		<div class="pmprocm_modal_content">
			<span class="pmprocm_modal_close">&times;</span>
			<?php
				$template = pmpro_loadTemplate( 'checkout', 'local', 'pages' );
				echo $template;
			?>
		</div>
	</div>
	
	<script>
		jQuery(document).ready(function() {
			// Make sure the form submits to the checkout page
			jQuery('#pmpro_form').attr('action', '<?php echo esc_url( pmpro_url( 'checkout', '?pmpro_level=' . intval( $_REQUEST['pmpro_level'] ) ) ); ?>');

			// Get the modal
			var modal = jQuery('.pmprocm_modal_bg');

			// Get the button that opens the modal
			var btn = jQuery('.pmprocm_modal_btn');

			// Get the <span> element that closes the modal
			var span = jQuery('.pmprocm_modal_close');

			// When the user clicks on the button, open the modal 
			btn.click(function() {
				modal.show();
			});

			// When the user clicks on <span> (x), close the modal
			span.click(function() {
				modal.hide();
			});

			// When the user clicks anywhere outside of the modal, close it
			modal.on('click', function(e) {			  
				if (e.target !== this)
					return;

				modal.hide();
			});
		});
	</script>

	<?php
}
add_action( 'wp_footer', 'pmprocm_content' );
