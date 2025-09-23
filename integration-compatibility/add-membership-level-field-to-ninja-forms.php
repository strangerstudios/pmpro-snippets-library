<?php
/**
 * Add a hidden field in Ninja Forms to capture Membership Level (if user is logged in).
 *
 * title: Ninja Forms - Add Hidden Field for Membership Level
 * layout: snippet
 * collection: integration-compatibility
 * category: ninja-forms
 * link: https://www.paidmembershipspro.com/ninja-forms-add-hidden-field-for-membership-level/
 *
 * You can add this recipe to your site by creating a custom plugin
 * or using the Code Snippets plugin available for free in the WordPress repository.
 * Read this companion article for step-by-step directions on either method.
 * https://www.paidmembershipspro.com/create-a-plugin-for-pmpro-customizations/
 */

/**
 * Create Membership Level field for Ninja Forms.
 */
function my_nf_register_fields( $fields ) {
	if ( class_exists( 'PMProMembershipLevelNFInput' ) ) {
		$fields['pmpromembershiplevel'] = new PMProMembershipLevelNFInput();
	}
	return $fields;
}
add_filter( 'ninja_forms_register_fields', 'my_nf_register_fields' );

/**
 * Add class for input field.
 */
function my_pmpro_add_membership_level_class_for_nf() {
	if ( ! class_exists( 'NF_Abstracts_Input' ) ) {
		return;
	}

	class PMProMembershipLevelNFInput extends NF_Abstracts_Input {
		protected $_name          = 'pmpromembershiplevel';
		protected $_nicename      = 'Membership Level';
		protected $_section       = 'userinfo';
		protected $_icon          = 'eye-slash';
		protected $_type          = 'hidden';
		protected $_templates     = 'hidden';
		protected $_wrap_template = 'wrap-no-label';
		protected $_settings_only = array(
			'key',
			'label',
			'admin_label',
		);
		public function __construct() {
			parent::__construct();
			$this->_nicename = esc_html__( 'Membership Level', 'ninja-forms' );
			$this->_settings['label']['width'] = 'full';
		}
	}
}
add_action( 'plugins_loaded', 'my_pmpro_add_membership_level_class_for_nf', 1 );

/**
 * Set value for pmpromembershiplevel field.
 */
function my_nf_default_value_membership_level( $default_value, $field_type, $field_settings ) {
	global $current_user;
	// Get the current user's membership level object.
	if ( function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
		$level = pmpro_getMembershipLevelForUser( $current_user->ID );
	} else {
		$level = false;
	}
	// Get the current user's membership level name.
	if ( ! empty( $level ) ) {
		$current_membership_level_name = $level->name;
	} else {
		$current_membership_level_name = false;
	}
	if ( 'pmpromembershiplevel' === $field_type && in_array( 'pmpromembershiplevel', $field_settings ) && ! empty( $current_membership_level_name ) ) {
		$default_value = $current_membership_level_name;
	}
	return $default_value;
}
add_filter( 'ninja_forms_render_default_value', 'my_nf_default_value_membership_level', 10, 3 );
