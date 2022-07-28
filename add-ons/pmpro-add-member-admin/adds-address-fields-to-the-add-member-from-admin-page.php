<?php //do not copy

/**
 * Add billing fields to the Add Member from Admin Add On page
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 *
 */

/*
 * Adds address fields to the Add Member from Admin page
 */
function my_pmpro_add_member_add_address_fields( $user, $user_id ) {
	?>
	<tr id="address">
		<th>
			<label for="company">Address</label>
		</th>
		<td>
			<input type="text" id="address1" name="address1">
		</td>
	</tr>
	<tr id="city_name">
		<th>
			<label for="company">City</label>
		</th>
		<td>
			<input type="text" id="city_name" name="city_name">
		</td>
	</tr>
	<tr id="zip_code">
		<th>
			<label for="company">Zip Code</label>
		</th>
		<td>
			<input type="text" id="addrzip_codeezip_codess1" name="zip_code">
		</td>
	</tr>
	<tr id="country">
		<th>
			<label for="company">Country</label>
		</th>
		<td>
			<input type="text" id="country" name="country">
		</td>
	</tr>
	<?php
}
add_action( 'pmpro_add_member_fields', 'my_pmpro_add_member_add_address_fields', 10, 2 );
