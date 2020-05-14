<?php
/*
	Plugin Name: External Link Pop-ups
	Description: Enables an interstitial pop-up message to users when clicking a link to an external URL. Requires Advanced Custom Fields.
	Version: 2.0.3
	Author: Vital
	Author URI: https://vtldesign.com
	Text Domain: vital
*/

if (! defined('ABSPATH')) {
	exit;
}

require 'plugin-update-checker/plugin-update-checker.php';

$elp_update_checker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/VitalDevTeam/external-link-popups/',
	__FILE__,
	'external-link-popups'
);

class Vital_External_Link_Popups {

	private $plugin_path;
	private $plugin_url;
	private $plugin_version;
	private $suffix;

	public function __construct() {

		$this->plugin_path = plugin_dir_path(__FILE__);
		$this->plugin_url  = plugin_dir_url(__FILE__);
		$this->version = '1.0.0';
		$this->suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		require $this->plugin_path . 'admin.php';

		if (function_exists('acf')) {
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_action_link']);
			add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
			add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
			add_action('wp_footer', [$this, 'add_popup']);
		} else {
			add_action('admin_notices', [$this, 'no_acf_notice']);
		}
	}

	/**
	 * Adds admin notice if ACF is not installed.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function no_acf_notice() {
		echo '<div class="error notice"><p><strong>External Link Pop-ups</strong> requires an active installation of Advanced Custom Fields. Please install ACF or deactivate this plugin. To disable popups, deactivate this plugin.</p></div>';
	}

	/**
	 * Adds settings page link to plugins page.
	 *
	 * @since 1.0.0
	 * @param array $links Array of links.
	 * @return array Filtered array of links.
	 */
	public function add_action_link($links) {
		$custom_link = [
			'<a href="' . admin_url('admin.php?page=external-link-popups') . '">Settings</a>',
		];

		return array_merge($custom_link, $links);
	}

	/**
	 * Enqueues plugin scripts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		global $is_IE;

		wp_enqueue_script(
			'elp_js',
			$this->plugin_url . 'assets/js/external-link-popups' . $this->suffix . '.js',
			false,
			$this->version,
			true
		);

		if ($is_IE) {

			wp_enqueue_script(
				'elp_url_polyfill',
				$this->plugin_url . 'assets/js/external-link-popups-ie-polyfills' . $this->suffix . '.js',
				false,
				$this->version,
				true
			);
		}

		wp_localize_script('elp_js', 'ELP', [
			'homeUrl'    => get_home_url(),
			'exceptions' => get_field('field_pbYIKZqYzXkaFPOG_elp_exceptions', 'option'),
		]);
	}

	/**
	 * Enqueues plugin stylesheets.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_styles() {

		wp_enqueue_style(
			'elp_css',
			$this->plugin_url . 'assets/css/external-link-popups' . $this->suffix . '.css',
			false,
			$this->version
		);
	}

	/**
	 * Adds popup markup to page footer.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_popup() {

		if ($content = get_field('field_YLj0jJa1DrG8aNvw_elp_popup_content', 'option')) {

			$content = str_replace('{destination}', '<span class="elp-popup-destination"></span>', $content);
			$content = str_replace('{countdown}', '<span id="elp-popup-countdown"></span>', $content);

			$button_cancel = sprintf(
				'<button type="button" class="elp-popup-cancel elp-popup-button"><span>%s</span></button>',
				get_field('field_xOrOdG5f3h2y6aYP_elp_cancel_button_text', 'option')
			);

			$button_ok = sprintf(
				'<a href="#" class="elp-popup-ok elp-popup-button">%s</a>',
				get_field('field_xOrOdG5f3h2y6aYP_elp_ok_button_text', 'option')
			);

			$delay = get_field('field_YgXzJ7K7LnwGO42Z_elp_redirect_delay', 'option');

			echo <<<EOT
<div id="elp-popup" class="elp-popup" data-redirect="{$delay}">
	<div class="elp-popup-outer-wrapper">
		<div class="elp-popup-inner-wrapper">
			<div class="elp-popup-container">
				<div id="elp-popup-content" class="elp-popup-content entry" role="dialog" aria-modal="true" aria-hidden="true" aria-label="External Link Notice">
					{$content}
					<div class="elp-popup-content-actions">
						<span class="elp-popup-content-action elp-popup-content-cancel">{$button_cancel}</span>
						<span class="elp-popup-content-action elp-popup-content-ok">{$button_ok}</span>
					</div>
				</div>
				<div class="elp-popup-overlay-close">{$button_cancel}</div>
			</div>
		</div>
	</div>
</div>
<div id="elp-popup-overlay" class="elp-popup-overlay"></div>
EOT;
		}
	}
}

new Vital_External_Link_Popups();
