<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class FormidableUserListManager {
	/**
	 * @var FormidableUserListLoader
	 */
	protected $loader;

	protected $plugin_slug;
	private static $plugin_short = 'FormidableUserList';

	protected $version;

	public function __construct() {

		$this->plugin_slug = 'formidable-user-list';
		$this->version     = '1.0';

		$this->load_dependencies();
		$this->define_admin_hooks();

	}

	static function getShort() {
		return self::$plugin_short;
	}

	private function load_dependencies() {

		require_once plugin_dir_path( __FILE__ ) . 'FormidableUserListLoader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class/FormidableUserListAdmin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class/FormidableUserListSettings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class/GManagerFactory.php';

		$this->loader = new FormidableUserListLoader();
	}

	private function define_admin_hooks() {
		$gManager = GManagerFactory::buildManager('FormidableUserListManager', 'formidable_user_list', self::getShort());
		$admin = new FormidableUserListAdmin( $this->get_version(), $this->plugin_slug, $gManager );

		if ( !class_exists( "FrmProAppController" ) ) {
			$this->loader->add_action( 'frm_pro_available_fields', $admin, 'add' . self::getShort() . 'Field' );
		}
		else{
			$this->loader->add_action( 'frm_available_fields', $admin, 'add' . self::getShort() . 'Field' );
		}
		$this->loader->add_action( 'frm_before_field_created', $admin, 'set' . self::getShort() . 'Options' );
		$this->loader->add_action( 'frm_display_added_fields', $admin, 'show' . self::getShort() . 'AdminField' );
		$this->loader->add_action( 'frm_field_options_form', $admin, 'field' . self::getShort() . 'OptionForm', 10, 3 );
		$this->loader->add_action( 'frm_update_field_options', $admin, 'update' . self::getShort() . 'Options', 10, 3 );
		$this->loader->add_action( 'frm_form_fields', $admin, 'show' . self::getShort() . 'FrontField', 10, 2 );
		$this->loader->add_action( 'frm_display_value', $admin, 'display' . self::getShort() . 'AdminField', 10, 3 );
		$this->loader->add_action( 'frm_add_settings_section', $admin, 'add' . self::getShort() . 'SettingPage', 10, 3 );
		$this->loader->add_filter( 'frmpro_fields_replace_shortcodes', $admin, 'shortCode' . self::getShort() . 'Replace', 10, 4 );
		$this->loader->add_filter( 'plugin_action_links', $admin, 'add' . self::getShort() . 'SettingLink', 9, 2 );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_version() {
		return $this->version;
	}

	/**
	 * Translate string to main Domain
	 *
	 * @param $str
	 *
	 * @return string|void
	 */
	public static function t( $str ) {
		return __( $str, 'formidable_user_list-locale' );
	}
}