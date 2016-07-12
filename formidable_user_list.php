<?php
/*
 * Plugin Name:       Formidable user list
 * Plugin URI:        https://github.com/gfirem/formidable_user_list
 * Description:       Select user from select and save id of user
 * Version:           1.06
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

require_once 'plugin-update-checker/plugin-update-checker.php';

$myUpdateChecker = PucFactory::buildUpdateChecker( 'http://gfirem.com/update-services/?action=get_metadata&slug=formidable_user_list', __FILE__ );
$myUpdateChecker->addQueryArgFilter( 'appendQueryArgsCredentials' );

/**
 * Append the order key to the update server URL
 *
 * @param $queryArgs
 *
 * @return
 */
function appendQueryArgsCredentials( $queryArgs ) {
	$queryArgs['order_key'] = get_option( FormidableUserListManager::getShort() . 'licence_key', '' );

	return $queryArgs;
}

function FormidableUserListBootLoader() {
	add_action( 'plugins_loaded', 'setFormidableUserListTranslation' );
	$manager = new FormidableUserListManager();
	$manager->run();
}

register_activation_hook(__FILE__, "checkRequired");

function checkRequired(){
	if(!class_exists("FrmProAppController")){
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'errorNotice' );
	}
}

function errorNotice() {
	$class = 'notice notice-error';
	$message = FormidableUserListManager::t( 'This plugins required Formidable Pro to run!' );
	printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
}

/**
 * Add translation files
 */
function setFormidableUserListTranslation() {
	load_plugin_textdomain( 'formidable_user_list-locale', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

FormidableUserListBootLoader();