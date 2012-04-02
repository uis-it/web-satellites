<?php

/*
 * see http://framework.zend.com/manual/en/zend.auth.adapter.ldap.html
 */
class CMS_auth_adapter extends Zend_Auth_Adapter_Ldap {

	public function __construct($username,$password) {
		$options = Zend_Registry::get('OPTIONS');
		parent::__construct( $options['ldap'], $username, $password);
	}


	public function authenticate () {
		$result = parent::authenticate();
		if ($result->isValid()) {
			$result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->getPrincipal());
		}
// 		try {
// 			$myIdentity = $this->getUserInfo();
// 			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,$myIdentity);
// 		} catch (Exception $e) {
// 			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null, array($e->getMessage())); 
// 		}	
		return $result;
	}

	protected function _prepareOptions($ldap, $options) {
		$this->_currentOptions = $options;
		if (isset($options['grouppattern'])) {
			$this->_groupPattern = $options['grouppattern'];
			unset($options['grouppattern']);
		}
		return parent::_prepareOptions($ldap, $options);		
	}
	
	private function getPrincipal() {
		
		if (isset($this->_currentOptions)) {
			$memberAttr = $this->_currentOptions['memberAttr'];
		} else {
			$memberAttr = 'memberOf';
		}
		
		$accountObject = $this->getAccountObject(array('displayname', 'mail', $memberAttr));

		$u = Model_User::checkUserExist($this->_username, $accountObject->displayname);
		
		$principal = new CMS_Auth_UisPrincipal($this->_username);
		$principal->_fullname = $accountObject->displayname;

		foreach ($accountObject->{$memberAttr} as $membership) {
			preg_match(
			$this->_groupPattern
			,$membership
			,$matches
			);
			if (count($matches))  {
				$saGroup = $matches[1];
				switch ($saGroup) {
					case 'administrator': {
						$principal->_role = 'administrator';
						break;
					}
					case 'clearcache':
					case 'forum':
					case 'webmeeting': {
						if (!$principal->isAdministrator()) {
							$principal->_role = 'user';
							$principal->_access[] = $saGroup;
						}
						break;
					}
					default:
						break;
				}
				//checks if it s a webmeeting ID
				preg_match(
			    		'/webmeeting-(.*)/'
				,$saGroup
				,$matches
				);
				if (count($matches)) {
					$principal->_access[] = 'webmeting';
					$principal->_wpAccess[] = $matches[1];
				}
			}
		}

		return $principal;
	}
	
}

