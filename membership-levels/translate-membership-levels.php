<?php
/**
 * Translate membership levels
 *
 * title: Translate membership levels
 * layout: snippet
 * collection: levels
 * category: localization
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

// Defining translated levels.
global $pmpro_translated_levels;
$pmpro_translated_levels['es_ES'] = array(
	1 => array(
		'name' => 'Primero Nivel',
		'description' => 'La descripción del nivel.',
		'confirmation' => 'Mensaje de confirmación',
	),
	2 => array(
		'name' => 'Segundo Nivel',
		'description' => 'La descripción del nivel.',
		'confirmation' => 'Mensaje de confirmación.',
	),
);

// Filter $pmpro_levels global.
function my_init_membership_level_translate() {
	global $pmpro_translated_levels;
	
	if ( empty( $pmpro_translated_levels ) ) {
		return;
	}

	$site_locale = get_locale();
	
	foreach ( $pmpro_translated_levels as $locale => $localized_levels ) {
		if ( $locale == $site_locale ) {	
			global $pmpro_levels;
			$pmpro_levels = pmpro_getAllLevels( true, true );
			
			// Translate
			foreach ( $localized_levels as $level_id => $localized_level ) {
				foreach( $pmpro_levels as $level_key => $pmpro_level ) {
					if ( $level_id == $pmpro_level->id ) {
						foreach ( $localized_level as $key => $value ) {
							$pmpro_levels[$level_key]->$key = $value;
						}
					}
				}				
			}			
		}
	}
}
add_action( 'init', 'my_init_membership_level_translate', 1 );

// Filter levels page and user levels.
function my_pmpro_levels_array( $levels ) {
	global $pmpro_translated_levels;

	if ( empty( $pmpro_translated_levels ) ) {
		return;
	}
	
	$site_locale = get_locale();
	
	foreach ( $pmpro_translated_levels as $locale => $localized_levels ) {
		if ( $locale == $site_locale ) {			
			// Translate
			foreach ( $localized_levels as $level_id => $localized_level ) {
				foreach( $levels as $level_key => $level ) {
					if ( $level_id == $level->id ) {
						foreach ( $localized_level as $key => $value ) {
							$levels[$level_key]->$key = $value;
						}
					}
				}				
			}
		}
	}	

	return $levels;
}
add_filter( 'pmpro_levels_array', 'my_pmpro_levels_array' );					// filter all levels
add_filter( 'pmpro_get_membership_levels_for_user', 'my_pmpro_levels_array' );	// filter user levels

// Filter checkout level
function my_pmpro_checkout_level_translate( $level ) {
	global $pmpro_translated_levels;

	if ( empty( $pmpro_translated_levels ) ) {
		return $level;
	}

	$site_locale = get_locale();
	
	foreach ( $pmpro_translated_levels as $locale => $localized_levels ) {
		if ( $locale == $site_locale ) {
			if ( ! empty( $localized_levels[$level->id] ) ) {
				foreach ( $localized_levels[$level->id] as $key => $value ) {
					$level->$key = $value;
				}				
			}
		}
	}
	return $level;
}
add_filter( 'pmpro_checkout_level', 'my_pmpro_checkout_level_translate' );