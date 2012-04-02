<?php 

class ForumController extends Zend_Controller_Action
{
	
 	public function init()
    {
        /* Initialize action controller here */		
    }
    
    public function adminAction (){
    	array_push($this->view->aJsToInclude, "/javascript/jquery-ui-1.8.5.custom.tabs.min.js");		
    	array_push($this->view->aCssToInclude, "/css/redmond/jquery-ui-1.8.5.custom.tabs.css");
    	array_push($this->view->aCssToInclude, "/css/redmond/Forum.css");
    	$this->view->Topics = Model_Forum::getNewTopics();
    	$this->view->Comments = Model_Forum::getNewComments();
   	}
   	
   	public function getdashboardthreadAction(){
   		$innlegg_ID = $this->_request->getParam('Id');
   		$this->view->thread = Model_Forum::getDashboardThread($innlegg_ID);
   		$this->render('editdashboardthread');
   	}
   	
   	public function updatedashboardthreadAction(){
   		$thread = array('innlegg_ID'=>	($_POST['innlegg_ID']),
   						'epost'      =>	($_POST['epost']),
   						'fornavn'    => ($_POST['fornavn']),
						'etternavn'  =>	($_POST['etternavn']),
						'tittel'     =>	($_POST['tittel']),
						'overskrift' =>	($_POST['overskrift']),
   						'ingress'    => ($_POST['ingress']),
   						'innhold'    => ($_POST['innhold']),
						'dato'       =>	($_POST['dato']),
						'godkjent' 	 =>	($_POST['godkjent']),
   						'sak'        =>	($_POST['sak']),
						'webSite'    =>	($_POST['webSite']),
					);
		Model_Forum::updateDashboardThread($thread);	
		array_push($this->view->aJsToInclude, "/javascript/jquery-ui-1.8.5.custom.tabs.min.js");
    	array_push($this->view->aCssToInclude, "/css/redmond/jquery-ui-1.8.5.custom.tabs.css");
    	array_push($this->view->aCssToInclude, "/css/redmond/Forum.css");
    	$this->_redirect("/forum/admin/#tabs-1");
   	}

   	public function acceptedAction (){  
   		$this->getAcceptedOrRejectedList('1','Godkjente innlegg','1');
   	}
   	
	public function rejectedAction (){ 
		$this->getAcceptedOrRejectedList('2','Avviste innlegg','2');
   	}
   	
   	public function getAcceptedOrRejectedList($godkjent, $Title, $TabNr){
   		$this->_helper->layout()->disableLayout();
   		array_push($this->view->aJsToInclude, "/javascript/jquery-ui-1.8.5.custom.tabs.min.js");
    	array_push($this->view->aCssToInclude, "/css/redmond/jquery-ui-1.8.5.custom.tabs.css");
    	array_push($this->view->aCssToInclude, "/css/redmond/Forum.css");
    	$this->view->List 	= Model_Forum::getAcceptedOrRejectedList($godkjent);   
    	$this->view->Title	= $Title;
    	$this->view->TabNr	= $TabNr;  //To know if it was accepted or rejected tab    	
    	$this->render('getAcceptedOrRejectedList');
   	}
   	
	public function editthreadAction (){ 	
		$godkjent_ID = $this->_request->getParam('Id');
		$this->view->TabNr  = $this->_request->getParam('Tab');
		$this->view->thread = Model_Forum::getThread($godkjent_ID);			
	}
	
	public function updatethreadAction (){ 	
		$TabNr= $_POST['tab'];			
		$thread = array('fornavn'   => ($_POST['fornavn']),
						'etternavn'  =>	($_POST['etternavn']),
						'tittel'     =>	($_POST['tittel']),
						'epost'      =>	($_POST['epost']),
						'sak'        =>	($_POST['sak']),
						'dato'       =>	($_POST['dato']),
						'godkjent_ID'=> ($_POST['godkjent_ID']),
						'overskrift' =>	($_POST['overskrift']),
						'godkjent' 	 =>	($_POST['godkjent']),
						'webSite'    =>	($_POST['webSite']),
						'innhold'    => ($_POST['innhold']),
						'ingress'    => ($_POST['ingress']));	
		Model_Forum::updateThread($thread);	
		array_push($this->view->aJsToInclude, "/javascript/jquery-ui-1.8.5.custom.tabs.min.js");
    	array_push($this->view->aCssToInclude, "/css/redmond/jquery-ui-1.8.5.custom.tabs.css");
    	array_push($this->view->aCssToInclude, "/css/redmond/Forum.css");
    	$this->_redirect("/forum/admin/#ui-tabs-".$TabNr);
	}
	
	public function editcommentsAction (){ 
		array_push($this->view->aJsToInclude, "/javascript/jquery-ui-1.8.5.custom.tabs.min.js");
    	array_push($this->view->aCssToInclude, "/css/redmond/jquery-ui-1.8.5.custom.tabs.css");
    	array_push($this->view->aCssToInclude, "/css/redmond/Forum.css");    			
		$godkjentID 				= $this->_request->getParam('id');
		$theCommentType				= $this->_request->getParam('type');
		$theTopicType 				= $this->_request->getParam('topicType');
		$this->view->result			= Model_Forum::getComments($godkjentID, $theCommentType);
		$this->view->godkjent_ID 	= $godkjentID ;
		$this->view->theCommentType	= $theCommentType ;
		$this->view->theTopicType	= $theTopicType ;
	}	
	
	public function updatecommentAction(){
		$theCommentType = $this->_request->getParam('theCommentType');
		$TopicType 		= $this->_request->getParam('TopicType');
		$godkjentID 	= $this->_request->getParam('godkjent_ID');
		$bApproved 		= $_POST['bApproved'];
		if($bApproved == NULL) // None of Radio Buttons are checked
			$bApproved = '9';
		$comment = array('commentContent' => ($_POST['content']),
						 'authorWeb'      => ($_POST['web']),
						 'bApproved'      => $bApproved ,
						 'id'  			  => ($_POST['commentID']),
						 'title' 		  => ($_POST['commentTitle']));
		Model_Forum::updateComment($comment);
		$this->_redirect("/forum/editcomments/id/".$godkjentID."/topicType/".$TopicType."/type/".$theCommentType);
	}
}