<?php
if (! defined('ABSPATH')) {
	exit;
}

class Vital_External_Link_Popups_Settings {

	public function __construct() {

		if (function_exists('acf')) {
			add_action('init', [$this, 'settings']);
		}
	}

	/**
	 * Adds settings page and fields.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function settings() {

		acf_add_options_page([
			'page_title'      => 'External Link Pop-ups',
			'menu_title'      => 'External Link Pop-ups',
			'menu_slug'       => 'external-link-popups',
			'parent_slug'     => 'options-general.php',
			'update_button'   => 'Save',
			'updated_message' => 'Settings Saved.',
			'capability'      => 'edit_posts',
			'redirect'        => false,
		]);

		acf_add_local_field_group([
			'key'                   => 'group_elp_settings',
			'title'                 => 'External Link Pop-ups Settings',
			'fields'                => [
				[
					'key'     => 'field_Yxg5mm8GսM6VnSXf_elp_msg',
					'label'   => '',
					'name'    => 'elp_msg',
					'type'    => 'message',
					'message' => '<p>All clicks on external links throughout the website will be interrupted by an interstitial pop-up message indicating to the user that they are leaving this website. To disable this feature, deactivate this plugin.</p>',
				],
				[
					'key'       => 'field_LVYG7AWcU3taZ9ah_elp_tab1',
					'label'     => 'Content',
					'name'      => 'elp_tab1',
					'type'      => 'tab',
					'placement' => 'top',
					'endpoint'  => 0,
				],
				[
					'key'          => 'field_YLj0jJa1DrG8aNvw_elp_popup_content',
					'label'        => 'Pop-Up Content',
					'name'         => 'elp_popup_content',
					'type'         => 'wysiwyg',
					'instructions' => 'These tags are available to use within your text:<br><br><code>{destination}</code> Displays the destination URL<br><br><code>{countdown}</code>. Displays the redirect delay countdown in seconds. This can only be used once.',
					'tabs'         => 'all',
					'toolbar'      => 'full',
					'media_upload' => 1,
					'delay'        => 0,
				],
				[
					'key'           => 'field_xOrOdG5f3h2y6aYP_elp_cancel_button_text',
					'label'         => 'Cancel Button Label',
					'name'          => 'elp_cancel_button_text',
					'type'          => 'text',
					'default_value' => 'Cancel',
					'placeholder'   => 'Cancel',
					'required'      => 1,
					'wrapper'       => [
						'width' => 50,
					],
				],
				[
					'key'           => 'field_xOrOdG5f3h2y6aYP_elp_ok_button_text',
					'label'         => 'OK Button Label',
					'name'          => 'elp_ok_button_text',
					'type'          => 'text',
					'default_value' => 'OK',
					'placeholder'   => 'OK',
					'required'      => 1,
					'wrapper'       => [
						'width' => 50,
					],
				],
				[
					'key'       => 'field_WAMmrydrwϳd6fICQ_elp_tab2',
					'label'     => 'Settings',
					'name'      => 'elp_tab2',
					'type'      => 'tab',
					'placement' => 'top',
					'endpoint'  => 0,
				],
				[
					'key'           => 'field_YgXzJ7K7LnwGO42Z_elp_redirect_delay',
					'label'         => 'Automatic Redirect Delay',
					'name'          => 'elp_redirect_delay',
					'type'          => 'number',
					'instructions'  => 'Number of seconds to wait before user is redirected to the destination URL. Set to <code>0</code> to disable.',
					'required'      => 1,
					'default_value' => 0,
					'min'           => 0,
					'step'          => 1,
				],
				[
					'key'          => 'field_pbYIKZqYzXkaFPOG_elp_exceptions',
					'label'        => 'Exceptions',
					'name'         => 'elp_exceptions',
					'type'         => 'repeater',
					'instructions' => '<p style="color: #666; font-size: 13px; font-style: italic;">To prevent the popup on specific external URLs, add them to the list below. 2 types of matches are available:</p><ul style="color: #666; font-size: 13px; font-style: italic;"><li><strong>Full URL:</strong> The <em>exact</em> URL you want to match. Pay careful attention to trailing slashes, and protocols (http:// vs. https://).</li><li><strong>Regex:</strong> The regex pattern for URLs you want to match.</li></ul>',
					'layout'       => 'block',
					'button_label' => 'Add Exception',
					'sub_fields'   => [
						[
							'key'               => 'field_mWkxB4UF1YIYf8YX_elp_exception_url',
							'label'             => 'URL',
							'name'              => 'elp_exception_url',
							'type'              => 'url',
							'required'          => 1,
							'placeholder'       => 'https://example.com/destination-page/',
							'conditional_logic' => [
								[
									[
										'field'    => 'field_FT1OMUóӡJlk19xWl_elp_exception_match',
										'operator' => '==',
										'value'    => 'url',
									],
								],
							],
							'wrapper'           => [
								'width' => '75',
							],
						],
						[
							'key'               => 'field_mWkxB4UF1YIYf8YX_elp_exception_regex',
							'label'             => 'Pattern',
							'name'              => 'elp_exception_regex',
							'type'              => 'text',
							'required'          => 1,
							'placeholder'       => '(.*)',
							'prepend'           => '/',
							'append'            => '/',
							'conditional_logic' => [
								[
									[
										'field'    => 'field_FT1OMUóӡJlk19xWl_elp_exception_match',
										'operator' => '==',
										'value'    => 'regex',
									],
								],
							],
							'wrapper'           => [
								'width' => '75',
							],
						],
						[
							'key'           => 'field_FT1OMUóӡJlk19xWl_elp_exception_match',
							'label'         => 'Match',
							'name'          => 'elp_exception_match',
							'type'          => 'select',
							'choices'       => [
								'url'   => 'Full URL',
								'regex' => 'Regex',
							],
							'default_value' => [
								'url',
							],
							'wrapper'       => [
								'width' => '25',
							],
						],
					],
				],
			],
			'location'              => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'external-link-popups',
					],
				],
			],
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => 1,
		]);

	}
}

$plugin_settings_page = new Vital_External_Link_Popups_Settings();
