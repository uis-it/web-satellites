<?php
class CMS_Auth_UisPrincipal {

	public $_role = 'guest';
	public $_access = array();
	public $_wpAccess = array();
	public $_username;
	public $_fullname;

	public function __construct($username) {
		$this->_username = $username;
	}

	public function __toString() {
		return $this->_username;
	}

	public function isAdministrator() {
		return $this->_role == 'administrator';
	}

	public function isUser() {
		return $this->_role == 'user';
	}

	public function isGuest() {
		return $this->_role == 'guest';
	}

	public function hasAccess($access) {
		return array_search($access, $this->_access);
	}

	public function haswpAccess($wpAccess) {
		return array_search($wpAccess, $this->_wpAccess);
	}
}


