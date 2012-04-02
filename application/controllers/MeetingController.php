<?php

class MeetingController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function listAction()
    {      
        array_push($this->view->aJsToInclude, "/javascript/meeting.list.js");
        array_push($this->view->aJsToInclude, "/javascript/jquery.metadata.js");        
    }
    
    public function createAction()
    {
        $frmMeeting = new Form_Meeting();
        $frmMeeting->setAction('/meeting/create/');
        $frmMeeting->setMethod("post");
        if ($this->_request->isPost()) { 
			if ($frmMeeting->isValid($_POST)) {
				$meetingModel = new Model_Meeting();				
				$meetingModel->create(
					$frmMeeting->getValue('meetingName')
					,$frmMeeting->getValue('description_small')
					,$frmMeeting->getValue('description')
					,$frmMeeting->getValue('description_internal')
					,($frmMeeting->getValue('bShow') == 'Yes') ? 1 : 0
					,$this->generateDate(
						$frmMeeting->getValue('live_from_hour')
						,$frmMeeting->getValue('live_from_day')
					)
					,$this->generateDate(
						$frmMeeting->getValue('live_to_hour')
						,$frmMeeting->getValue('live_to_day')
					)	
					,$frmMeeting->getValue("photo_thumbnail_url")
					,$frmMeeting->getValue("photo_url")								
				);
				return $this->_redirect('/meeting/list/');     
			}
        }
        
        $this->view->form = $frmMeeting;
        array_push($this->view->aJsToInclude, "/javascript/meeting.js");
    }

    public function editAction()
    {
    	$frmMeeting = new Form_Meeting();
    	$id = $this->_request->getParam('id');
    	$frmMeeting->setAction('/meeting/edit/id/'.$id);
        $frmMeeting->setMethod("post");
        $meetingModel = new Model_Meeting();
        if ($this->_request->isPost()) { 
        	if ($frmMeeting->isValid($_POST)) {
        		
        		
        		$meetingModel = new Model_Meeting();				
				$meetingModel->updateM(
					$id
					,$frmMeeting->getValue('meetingName')
					,$frmMeeting->getValue('description_small')
					,$frmMeeting->getValue('description')
					,$frmMeeting->getValue('description_internal')
					,($frmMeeting->getValue('bShow') == 'Yes') ? 1 : 0
					,$this->generateDate(
						$frmMeeting->getValue('live_from_hour')
						,$frmMeeting->getValue('live_from_day')
					)
					,$this->generateDate(
						$frmMeeting->getValue('live_to_hour')
						,$frmMeeting->getValue('live_to_day')
					)	
					,$frmMeeting->getValue("photo_thumbnail_url")
					,$frmMeeting->getValue("photo_url")											
				);				
				return $this->_redirect('/meeting/list/');
        	}
        }
        else {
	        
	        $currentMeeting 	= $meetingModel->find($id)->current();
	        
	       	$frmMeeting->getElement('meetingName')->setValue($currentMeeting->title);
	       	$frmMeeting->getElement('description')->setValue($currentMeeting->description);       	       
	       	$frmMeeting->getElement('description_small')->setValue($currentMeeting->description_small);       	       
	       	$frmMeeting->getElement('live_from_day')->setValue(substr($currentMeeting->live_from,0,10));
	       	$frmMeeting->getElement('live_from_hour')->setValue('T'.substr($currentMeeting->live_from,11,5));       	
			$frmMeeting->getElement('live_to_day')->setValue(substr($currentMeeting->live_to,0,10));
	       	$frmMeeting->getElement('live_to_hour')->setValue('T'.substr($currentMeeting->live_to,11,5)); 	       	 
	       	$frmMeeting->getElement('description_internal')->setValue($currentMeeting->description_internal);   
	       	
	       	$frmMeeting->getElement('photo_thumbnail_url')->setValue($currentMeeting->photo_thumbnail_url);
	       	$frmMeeting->getElement('photo_url')->setValue($currentMeeting->photo_url);
	       		       	
	       	if ($currentMeeting->bShow == 1) {
	       		$frmMeeting->getElement('bShow')->setChecked(true);       	
	       	}
	       	else {
	       		$frmMeeting->getElement('bShow')->setChecked(false);
	       	}
        }       	
        
        $this->view->form 	= $frmMeeting;
        array_push($this->view->aJsToInclude, "/javascript/meeting.js");        
    }
	
    public function qaAction () {    	
    	$id 			= $this->_request->getParam('id');
    	$meetingModel 	= new Model_Meeting();
    	$this->view->currentMeeting = $meetingModel->find($id)->current();
    	array_push($this->view->aJsToInclude, "/javascript/jquery.metadata.js");
    	array_push($this->view->aJsToInclude, "/javascript/splitter.js");
    	array_push($this->view->aJsToInclude, "/javascript/meeting.js");   
    	array_push($this->view->aJsToInclude, "/javascript/jquery.tmpl.js"); 
    	array_push($this->view->aJsToInclude, "/javascript/jquery-ui-1.8.2.custom.min.js"); 
    }
    
    public function adminqaAction () {    	
    	$id = $this->_request->getParam('id');    	
    	$this->isUserEditorForMeeting($id);    	
    	
    	    	
    	$this->view->currentMeetingID 	= $id;
    	$this->view->signature 			= $this->getUserSignature($id);

    	array_push($this->view->aJsToInclude, "/javascript/jquery.metadata.js");
    	array_push($this->view->aJsToInclude, "/javascript/splitter.js");
    	array_push($this->view->aJsToInclude, "/javascript/meeting.js");   
    	array_push($this->view->aJsToInclude, "/javascript/jquery.tmpl.js"); 
    	array_push($this->view->aJsToInclude, "/javascript/jquery-ui-1.8.2.custom.min.js");
    	array_push($this->view->aCssToInclude, "/css/redmond/jquery-ui-1.8.2.custom.css"); 
    }
      
	/*
	 * Gets signature for a meeting and a person, 
	 * if it doesn't exist it creates one 
	 */
    private function getUserSignature($id) {
    	$auth	= Zend_Auth::getInstance();
    	$u 		= $auth->getIdentity();
    	$sig	= $u->_fullname;
    	$m 		= new Model_UserWebmeetingProperties();    	
    	$q 		= $m->getUserSignature($u->_username,$id);
    	if (count($q) == 0) {
    		$m->insertRow($u->_username,$id,$sig);
    	}
    	else {
    		$sig = $q[0]["signature"];
    	}    	
		return $sig;
    }
    
    /*
     * Parses a "date" from 2 formfields in dojo
     * this should be in another file ... utils or something like that
     * */
	private function generateDate($d,$h) {
    	$hour	= substr($d,1,2);
		$min 	= substr($d,4,2);
		$year	= substr($h,0,4);
		$month	= substr($h,5,2);
		$day	= substr($h,8,2);
		return $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $min . ':00';
    }	   
	
    private function isUserEditorForMeeting ($id) {
    	$bHasRights	= false;
    	$auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
        	$u = $auth->getIdentity();
        	if ($u->_role == 'administrator') {  
        		$bHasRights = true;
        	} else {
        		if ($u->_role == 'user') {
        			if (in_array($id, $u->accessWebMeeting))
        				$bHasRights = true;
        		}	
        	}
		}
		if (!$bHasRights) {
			throw new Zend_Exception("User not in role, can't administrate this WebMeeting");		
		}    	
    }
}









