<?php
/*
 * Plugin Name:       Formidable user list
 * Plugin URI:        https://github.com/gfirem/formidable_user_list
 * Description:       Select user from select and save id of user
 * Version:           1.3
 * Author:            Guillermo Figueroa Mesa
 * Author URI:        http://wwww.gfirem.com
 * Text Domain:       formidable_user_list-locale
 * License:           Apache License 2.0
 * License URI:       http://www.apache.org/licenses/
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'class/FormidableUserListManager.php';
require 'plugin-update-checker/plugin-update-checker.php';

$className = PucFactory::getLatestClassVersion('PucGitHubChecker');
$myUpdateChecker = new $className(
	'https://github.com/gfirem/formidable_user_list/',
	__FILE__,
	'master'
);



function FormidableUserListBootLoader() {
	add_action( 'plugins_loaded', 'setFormidableUserListTranslation' );
	$manager = new FormidableUserListManager();
	$manager->run();
}

/**
 * Add translation files
 */
function setFormidableUserListTranslation() {
	load_plugin_textdomain( 'formidable_user_list-locale', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

FormidableUserListBootLoader();