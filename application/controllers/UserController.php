<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
                	    if($auth->hasIdentity()) {
                	        $this->view->identity = $auth->getIdentity();
                	    }
    }

    public function createAction()
    {
        $userForm = new Form_User();
                                            if ($this->_request->isPost()) {
                                                if ($userForm->isValid($_POST)) {
                                                    $userModel = new Model_User();
                                                    $userModel->createUser(
                                                        $userForm->getValue('username'),
                                                        $userForm->getValue('password'),
                                                        $userForm->getValue('first_name'),
                                                        $userForm->getValue('last_name'),
                                                        $userForm->getValue('role')
                                                    );
                                                    return $this->_forward('list');        }
                                            }
                                            $userForm->setAction('/user/create');
                                            $this->view->form = $userForm;
    }

    public function listAction()
    {
        $currentUsers = Model_User::getUsers();
                                	    if ($currentUsers->count() > 0) {
                                	        $this->view->users = $currentUsers;
                                	    } else {
                                	        $this->view->users = null;
                                	    }
    }

    public function updateAction()
    {
        $userForm = new Form_User();
                            $userForm->setAction('/user/update');
                            $userForm->removeElement('password');
                            $userModel = new Model_User();
                            if ($this->_request->isPost()) {
                        
                        
                        if ($userForm->isValid($_POST)) {
                            $userModel->updateUser(
                                $userForm->getValue('id'),
                                $userForm->getValue('username'),
                                $userForm->getValue('first_name'),
                                $userForm->getValue('last_name'),
                                $userForm->getValue('role')
                                );
                                return $this->_forward('list');         }
                            } else {
                                $id = $this->_request->getParam('id');
                                $currentUser = $userModel->find($id)->current();
                                $userForm->populate($currentUser->toArray());
                            }
                        	    $this->view->form = $userForm;
    }

    public function passwordAction()
    {
        $passwordForm = new Form_User();
                	    $passwordForm->setAction('/user/password');
                	    $passwordForm->removeElement('first_name');
                	    $passwordForm->removeElement('last_name');
                	    $passwordForm->removeElement('username');
                	    $passwordForm->removeElement('role');
                	    $userModel = new Model_User();
                	    if ($this->_request->isPost()) {
                	        if ($passwordForm->isValid($_POST)) {
                	            $userModel->updatePassword(
                	                $passwordForm->getValue('id'),
                	                $passwordForm->getValue('password')
                	            );
                	            return $this->_forward('list');
                	        }
                	    } else {
                	        $id = $this->_request->getParam('id');
                	
                	
                	$currentUser = $userModel->find($id)->current();
                	        $passwordForm->populate($currentUser->toArray());
                	    }
                	    $this->view->form = $passwordForm;
    }

    public function deleteAction()
    {
        $id = $this->_request->getParam('id');
                	    $userModel = new Model_User();
                	    $userModel->deleteUser($id);
                	    return $this->_forward('list');
    }

    public function loginAction()
    {
    	$loginForm = new Form_User();

    	$loginForm->removeElement('first_name');
    	$loginForm->removeElement('last_name');
    	$loginForm->removeElement('role');
    	$loginForm->getElement('submit')->setLabel('Login');
    	
    	if ($loginForm->isValid($_POST)) {
    		$username = $loginForm->getValue('username');
    		$password = $loginForm->getValue('password');
    		
    		$adapter = new CMS_Auth_adapter($username, $password);
    		
    		$auth = Zend_Auth::getInstance();
    		$result = $auth->authenticate($adapter);
    		
    		if ($result->isValid()) {
    			$this->_helper->FlashMessenger('Successful Login');
    			$this->_redirect('/');
    			return;
    		}
    	}
    	
    	$this->view->form = $loginForm;
    }
	
    public function firstloginAction() {
    }
    
    public function logoutAction()
    {
     	$auth = Zend_Auth::getInstance();
     	$auth->clearIdentity();	
    	Zend_Session::forgetMe();
 		Zend_Session::destroy(true, false);
 		$this->_redirect('/');
		
 		return true;
    }


}













