<?php

/**
 * Plugin Name:     Mai Icons
 * Plugin URI:      https://bizbudding.com/mai-theme/
 * Description:     The required plugin for icons in Mai child themes.
 * Version:         2.0.0
 *
 * Author:          BizBudding
 * Author URI:      https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Mai_Icons_Plugin Class.
 *
 * @since 2.0.0
 */
final class Mai_Icons_Plugin {

	/**
	 * @var   Mai_Icons_Plugin The one true Mai_Icons_Plugin
	 * @since 2.0.0
	 */
	private static $instance;

	/**
	 * Main Mai_Icons_Plugin Instance.
	 *
	 * Insures that only one instance of Mai_Icons_Plugin exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since   2.0.0
	 * @static  var array $instance
	 * @uses    Mai_Icons_Plugin::setup_constants() Setup the constants needed.
	 * @uses    Mai_Icons_Plugin::includes() Include the required files.
	 * @uses    Mai_Icons_Plugin::hooks() Activate, deactivate, etc.
	 * @see     Mai_Icons_Plugin()
	 * @return  object | Mai_Icons_Plugin The one true Mai_Icons_Plugin
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			// Setup the setup.
			self::$instance = new Mai_Icons_Plugin;
			// Methods.
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since   2.0.0
	 * @access  protected
	 * @return  void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-icons' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * @since   2.0.0
	 * @access  protected
	 * @return  void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'mai-icons' ), '1.0' );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access  private
	 * @since   2.0.0
	 * @return  void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'MAI_ICONS_VERSION' ) ) {
			define( 'MAI_ICONS_VERSION', '2.0.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'MAI_ICONS_PLUGIN_DIR' ) ) {
			define( 'MAI_ICONS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'MAI_ICONS_PLUGIN_URL' ) ) {
			define( 'MAI_ICONS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'MAI_ICONS_PLUGIN_FILE' ) ) {
			define( 'MAI_ICONS_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Base Name
		if ( ! defined( 'MAI_ICONS_BASENAME' ) ) {
			define( 'MAI_ICONS_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
		}
	}

	/**
	 * Include required files.
	 *
	 * @access  private
	 * @since   2.0.0
	 * @return  void
	 */
	private function includes() {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	/**
	 * Run the hooks.
	 *
	 * @since   2.0.0
	 * @return  void
	 */
	public function hooks() {
		add_action( 'admin_init', [ $this, 'updater' ] );
		add_filter( 'plugin_action_links_mai-icons/mai-icons.php', [ $this, 'plugin_dependency_text' ], 100 );
	}

	/**
	 * Setup the updater.
	 *
	 * composer require yahnis-elsts/plugin-update-checker
	 *
	 * @since 2.0.0
	 *
	 * @uses https://github.com/YahnisElsts/plugin-update-checker/
	 *
	 * @return void
	 */
	public function updater() {
		// Bail if current user cannot manage plugins.
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		// Bail if plugin updater is not loaded.
		if ( ! class_exists( 'Puc_v4_Factory' ) ) {
			return;
		}

		// Setup the updater.
		$updater = Puc_v4_Factory::buildUpdateChecker( 'https://github.com/maithemewp/mai-icons/', __FILE__, 'mai-icons' );

		// Maybe set github api token.
		if ( defined( 'MAI_GITHUB_API_TOKEN' ) ) {
			$updater->setAuthentication( MAI_GITHUB_API_TOKEN );
		}

		// Add icons for Dashboard > Updates screen.
		if ( function_exists( 'mai_get_updater_icons' ) && $icons = mai_get_updater_icons() ) {
			$updater->addResultFilter(
				function ( $info ) use ( $icons ) {
					$info->icons = $icons;
					return $info;
				}
			);
		}
	}

	/**
	 * Changes plugin dependency text.
	 *
	 * @since 2.0.0
	 *
	 * @param array $actions Plugin action links.
	 *
	 * @return array
	 */
	function plugin_dependency_text( $actions ) {
		$actions['required-plugin'] = sprintf(
			'<span class="network_active">%s</span>',
			__( 'Mai Theme Dependency', 'mai-engine' )
		);

		return $actions;
	}
}

/**
 * The main function for that returns Mai_Icons_Plugin
 *
 * The main function responsible for returning the one true Mai_Icons_Plugin
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $plugin = Mai_Icons_Plugin(); ?>
 *
 * @since 2.0.0
 *
 * @return object|Mai_Icons_Plugin The one true Mai_Icons_Plugin Instance.
 */
function mai_icons() {
	return Mai_Icons_Plugin::instance();
}

// Get Mai_Icons_Plugin Running.
mai_icons();
