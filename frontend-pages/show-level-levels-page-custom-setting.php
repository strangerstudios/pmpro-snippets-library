<?php
/**
 * Add a custom setting to the edit level settings to show or hide a membership level on the level select page.
 * 
 * title: Add a setting to "hide" levels from display on the Memberships > Edit Level admin.
 * layout: snippet
 * collection: frontend-pages
 * category: levels, level-page, level-settings
 * 
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

//Save the pmpro_show_level_ID field
function pmpro_hide_level_from_levels_page_save( $level_id ) {
	if ( $level_id <= 0 ) {
		return;
	}
	$limit = $_REQUEST['pmpro_show_level'];
	update_option( 'pmpro_show_level_'.$level_id, $limit );
}
add_action( 'pmpro_save_membership_level','pmpro_hide_level_from_levels_page_save' );

//Display the setting for the pmpro_show_level_ID field on the Edit Membership Level page
function pmpro_hide_level_from_levels_page_settings() {
	?>
	<h3 class='topborder'><?php esc_html_e( 'Membership Level Visibility', 'pmpro' ); ?></h3>
	<table class='form-table'>
		<tbody>
			<tr>
				<th scope='row' valign='top'><label for='pmpro_show_level'><?php esc_html_e( 'Show Level', 'pmpro' );?>:</label></th>
				<td>
					<?php		
						if ( isset( $_REQUEST['edit'] ) ) {
							$edit = $_REQUEST['edit'];
							$pmpro_show_level = get_option( 'pmpro_show_level_' . $edit );
							if ( $pmpro_show_level === false ) {
								$pmpro_show_level = 1;
							}
						} else {
							$limit = '';
						}
					?>
					<select id='pmpro_show_level' name='pmpro_show_level'>
						<option value='1' <?php if ( $pmpro_show_level == 1 ) { ?>selected='selected'<?php } ?>><?php esc_html_e( 'Yes, show this level in the [pmpro_levels] display.', 'pmpro' );?></option>

						<option value='0' <?php if ( ! $pmpro_show_level ) { ?>selected='selected'<?php } ?>><?php _e( 'No, hide this level in the [pmpro_levels] display.', 'pmpro' );?></option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	<?php 
}
add_action( 'pmpro_membership_level_after_other_settings', 'pmpro_hide_level_from_levels_page_settings' );

//Filter the hidden levels from the pmpro_levels_array
function pmpro_hide_level_from_levels_page_levels( $levels ) {	
	$new_levels = array();
	foreach( $levels as $key => $level ) {
		$pmpro_show_level = get_option( 'pmpro_show_level_' . $level->id );
		
		//always include levels where the setting was never saved
		if ( $pmpro_show_level === false ) {
			$pmpro_show_level = true;
		}
		
		//build the new filtered levels array
		if ( ! empty( $pmpro_show_level ) ) {
			$new_levels[$key] = $level;
		}
	}
	return $new_levels;
}
add_filter( 'pmpro_levels_array', 'pmpro_hide_level_from_levels_page_levels' );