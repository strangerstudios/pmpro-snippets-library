<?php
/**
 * Automatically Set Price for Addon Packages.
 *
 * title: Automatically Set Price for Addon Packages
 * layout: snippet
 * collection: addons
 * category: pmpro-addon-packages
 * url: https://www.paidmembershipspro.com/automatically-set-price-for-addon-packages/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

 /*
 * Sets the addon package price of a post to $15 whenever a membership restriction is added.
 * Change the $addon_package_price line 24 to the price you want to set.
 * Does not overwrite an existing value. Can set page to $0 to keep a post as free while changeing which
 * levels have access.
 */
function my_pmproap_automatic_price() {
	$addon_package_price = 15; // change to your price
	?>
	<script>
		jQuery(document).ready(function() {	
			jQuery('input[name^=page_levels]').change(function() {
				if(jQuery('#pmproap_price').val() == ''){
					jQuery('#pmproap_price').val('<?php echo( $addon_package_price ) ?>');
				}
			});
		});
	</script>
	<?php
}
add_action( 'pmpro_after_require_membership_metabox', 'my_pmproap_automatic_price' );

