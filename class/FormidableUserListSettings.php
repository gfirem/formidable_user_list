<?php

class FormidableUserListSettings {

	public static function route() {

		$action = isset( $_REQUEST['frm_action'] ) ? 'frm_action' : 'action';
		$action = FrmAppHelper::get_param( $action );
		if ( $action == 'process-form' ) {
			return self::process_form();
		} else {
			return self::display_form();
		}
	}

	/**
	 * @internal var gManager GManager_1_0
	 */
	public static function display_form( ) {
		$gManager = GManagerFactory::buildManager('FormidableUserListManager', 'formidable_user_list', FormidableUserListManager::getShort());
		$key  = get_option( FormidableUserListManager::getShort() . 'licence_key' );
		?>
		<h3 class="frm_first_h3"><?= FormidableUserListManager::t( "Licence Data" ) ?></h3>
		<table class="form-table">
			<tr class="form-field" valign="top">
				<td width="150px"><label for="key"><?= FormidableUserListManager::t( "Order Key" ) ?></label></td>
				<td><input type="text" name="key" id="key" value="<?= $key ?>"/> <?= FormidableUserListManager::t( "Key status: " ).$gManager->getStatus() ?></td>
			</tr>
			<tr>
				<td colspan="2">
					<span ><?= FormidableUserListManager::t("Order key send to you with order confirmation, to get updates.") ?></span>
				</td>
			</tr>
		</table>
	<?php
	}

	public static function process_form() {
		if ( isset( $_POST['key'] ) && ! empty( $_POST['key'] ) ) {
			$gManager = GManagerFactory::buildManager('FormidableUserListManager', 'formidable_user_list', FormidableUserListManager::getShort());
			$gManager->activate($_POST['key']);
			update_option( FormidableUserListManager::getShort() . 'licence_key', $_POST['key'] );
		}
		self::display_form();
	}
}