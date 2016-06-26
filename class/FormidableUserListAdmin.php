<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormidableUserListAdmin {
	protected $version;
	private $slug;

	public function __construct( $version, $slug ) {
		$this->version = $version;
		$this->slug    = $slug;
	}

	/**
	 * Add new field to formidable list of fields
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function addFormidableUserListField( $fields ) {
		$fields['userlist'] = FormidableUserListManager::t( "User List" );

		return $fields;
	}

	/**
	 * Set the default options for the field
	 *
	 * @param $fieldData
	 *
	 * @return mixed
	 */
	public function setFormidableUserListOptions( $fieldData ) {
		if ( $fieldData['type'] == 'userlist' ) {
			$fieldData['name'] = 'UserList';

			$defaults = array(
				'roles' => FormidableUserListManager::t( "Select roles to get users" ),
			);

			foreach ( $defaults as $k => $v ) {
				$fieldData['field_options'][ $k ] = $v;
			}
		}

		return $fieldData;
	}

	/**
	 * Show the field placeholder in the admin area
	 *
	 * @param $field
	 */
	public function showFormidableUserListAdminField( $field ) {
		if ( $field['type'] != 'userlist' ) {
			return;
		}
		$field_name = 'item_meta[' . $field['id'] . ']';
		?>

		<div class="frm_html_field_placeholder">
			<div class="frm_html_field"><?= FormidableUserListManager::t( "Show list of user from selected role in frontend" ) ?></div>
		</div>
	<?php
	}

	/**
	 * Display the additional options for the new field
	 *
	 * @param $field
	 * @param $display
	 * @param $values
	 */
	public function fieldFormidableUserListOptionForm( $field, $display, $values ) {
		if ( $field['type'] != 'userlist' ) {
			return;
		}

		$defaults = array(
			'roles' => FormidableUserListManager::t( "Select roles to get users" ),
		);

		foreach ( $defaults as $k => $v ) {
			if ( ! isset( $field[ $k ] ) ) {
				$field[ $k ] = $v;
			}
		}
		?>
		<tr>
			<td><label><?= FormidableUserListManager::t( "Roles" ) ?></label></td>
			<td>
				<label for="label1_<?= $field['id'] ?>" class="howto"><?= FormidableUserListManager::t( "Select role to get users, by default Editor" ) ?></label>
				<select name="field_options[roles_<?php echo $field['id'] ?>]" class="frm_long_input" id="roles_<?php echo $field['id'] ?>">
					<?php wp_dropdown_roles( $field['roles'] ); ?>
				</select>
			</td>
		</tr>
	<?php
	}

	/**
	 * Update the field options from the admin area
	 *
	 * @param $field_options
	 * @param $field
	 * @param $values
	 *
	 * @return mixed
	 */
	public function updateFormidableUserListOptions( $field_options, $field, $values ) {
		if ( $field->type != 'userlist' ) {
			return $field_options;
		}

		$defaults = array(
			'roles' => 'editor'
		);

		foreach ( $defaults as $opt => $default ) {
			$field_options[ $opt ] = isset( $values['field_options'][ $opt . '_' . $field->id ] ) ? $values['field_options'][ $opt . '_' . $field->id ] : $default;
		}

		return $field_options;
	}

	/**
	 * Add the HTML for the field on the front end
	 *
	 * @param $field
	 * @param $field_name
	 */
	public function showFormidableUserListFrontField( $field, $field_name ) {
		if ( $field['type'] != 'userlist' ) {
			return;
		}
		$field['value'] = stripslashes_deep( $field['value'] );
		$field['roles'] = stripslashes_deep( $field['roles'] );

		$users = $this->getUserList( $field['roles'] );
		?>
		<select id='field_<?= $field['field_key'] ?>' name='item_meta[<?= $field['id'] ?>]'>
			<?php
			foreach ( $users as $id => $key ) {
				$select = ( $id == $field['value'] ) ? 'selected="selected"' : "";
				echo '<option ' . $select . ' value="' . $id . '">' . $key . '</option>';
			}
			?>
		</select>
	<?php
	}

	/**
	 * Add the HTML to display the field in the admin area
	 *
	 * @param $value
	 * @param $field
	 * @param $atts
	 *
	 * @return string
	 */
	public function displayFormidableUserListAdminField( $value, $field, $atts ) {
		if ( $field->type != 'userlist' || empty( $value ) ) {
			return $value;
		}

		return $value;
	}

	/**
	 * Process shortCode with attr
	 *
	 * @param $value
	 * @param $tag
	 * @param $attr This be one of next: id, email, name, login
	 * @param $field
	 *
	 * @return string
	 */
	public function shortCodeFormidableUserListReplace($value, $tag, $attr, $field){
		if ( $field->type != 'userlist' || empty( $value ) ) {
			return $value;
		}

		$internal_attr = shortcode_atts( array(
			'show' => 'id',
		), $attr );

		if($internal_attr['show'] == 'id'){
			return $value;
		}

		$user = get_userdata($value);
		$user_field = $internal_attr['show'];

		return $user->$user_field;
	}

	/**
	 * Get user list for given role.
	 *
	 * @param $roles
	 *
	 * @return array
	 */
	private function getUserList( $roles ) {
		global $wpdb;
		$users   = get_users( array( 'fields' => array( 'ID', 'user_login', 'display_name' ), 'role__in' => array( $roles ), 'blog_id' => $GLOBALS['blog_id'], 'orderby' => 'display_name' ) );
		$options = array( '' => '' );
		foreach ( $users as $user ) {
			$options[ $user->ID ] = ( ! empty( $user->display_name ) ) ? $user->display_name : $user->user_login;
		}

		return $options;
	}
}