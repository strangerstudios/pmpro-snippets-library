<?php
/**
 * Import User Meta Fields that are Stored as an Array.
 *
 * In your CSV file, you may set the array value to {value1,value2,value3}
 *
 * title: Import User Meta Fields that are Stored as an Array.
 * layout: snippet
 * collection: add-ons, import-user-from-csv
 * category: members, import
 * link: https://www.paidmembershipspro.com/import-user-fields-stored-as-an-array-select2-multi-checkbox/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */
function my_import_user_meta_csv_array( $meta, $userdata ) {

	// Loop through meta to get arrays.
	foreach ( $meta as $key => $value ) {
		$reg = '/( { ( (?: [^{}]* | (?1) )* ) } )/x';
		preg_match_all( $reg, $value, $matches );

		if ( ! empty( $matches[0] ) ) {
			$string = substr( $matches[0][0], 1, -1 ); // remove curly braces.
			$array  = explode( ',', $string ); // convert to PHP array.

			$meta[ $key ] = $array; // assign the key to an array.
		}
	}

	return $meta;
}
add_filter( 'pmproiucsv_import_usermeta', 'my_import_user_meta_csv_array', 10, 2 );
