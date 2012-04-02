<?php
class CMS_Meeting
{
	public function meeting_createMeeting() {
		$m = new Model_Meeting();		
		return $m->create("test desde js","y si sale!!",true);
	}
	public function meeting_getMeetings() {
		$m = new Model_Meeting();
		$user 	= $this->getUser();
		$listM 	= $m->getMeetings()->toArray();
		if ($user->_role == 'administrator') {
			return $listM;
		}	
		else {			
			$a = array();
			foreach($listM as $m) {
				
				if (in_array($m['id'],$user->accessWebMeeting)) {
					array_push($a,$m);
				}
			}
			return $a;
		}	
	}
	

	public function meeting_getMeetingById($id) {		
		$m = Model_Meeting::getMeetingById($id);	
		if ($m[0]["bShow"] == 1) {
			return $m;
		}
		else {
			return null;
		}
		
		
			//var_dump($m["bShow"]);die;
		
	}
	
	public function meeting_getMeetingsFromDate($from) {
		$u = new Model_Meeting();
		return $u->getMeetingsFromDate($from);
	}
	
	
	public function getQuestionsByMeeting($id) {
		$q = new Model_Question();	
		return $q->getQuestionByMeeting($id);
	}
	
	public function getQuestion ($id) {		
		$q 						= new Model_Question();
		$a						= new Model_Answer();
		$u						= new Model_User();	
		$ret 					= array();
		$user 					= $this->getUser();
		$question 				= $q->find($id);
		$answer					= $a->getByQuestion($id);
		$_q 					= $question->current();		
		$ret["question"]		= $question->toArray(); 		
		$ret["bIsMine"] 		= $user->_username == $_q->fk_user;
		$ret["bIsBlocked"] 		= is_numeric($_q->fk_user);		
		$ret["answerContent"]	= "";
		if (count($answer) > 0) {
			$answer	= $answer[0];
			$ret["answerContent"] = $answer["content"];
		}		
		$ret["user_name"]		= "";		
		if ($_q->fk_user) {
			$tmpUser = $u->find($_q->fk_user)->current()->toArray();			
			$ret["user_name"]		= $tmpUser["fullname"];
		}		
		if ($_q->fk_user == 0) {
			$ret["bIsBlocked"] 		= false;
		}
		return $ret;
	}
	public function captureQuestion($id) {		
		$q			= new Model_Question();
		$user 		= $this->getUser();		
		//TODO: check if the user has right to capture this question 
		
		$question 	= $q->find($id)->current();
		$question->fk_user = $user->_username;
		$question->save();
		return "OK";
	}
	
	public function meeting_saveContent($id,$content,$contentType) {
		//save by field, cound make more fields if need.		
		if ($contentType == "signatureText") {
			$q = new Model_UserWebmeetingProperties();
			$user 		= $this->getUser();	
			$q->updateSignature($user->_username,$id,$content);						
			return true;
		}
		
		$m = new Model_Meeting();
		dLog($contentType);
		
		$meeting = $m->find($id)->current(); 
		if ($contentType == "welcomeText") {
			$meeting->welcomeText = $content;		
		} 
		if ($contentType == "goodbyeText") {			
			$meeting->goodbyeText = $content;
		}
		if ($contentType == "status") {
			$meeting->bIsLive 				= ($content['bIsLive']) ? 1 : 0;
			$meeting->bAcceptQuestion 		= ($content['bAcceptQuestion']) ? 1 : 0;
			$meeting->bTabAcceptedQuestions	= ($content['bTabAcceptedQuestions']) ? 1 : 0;
			$meeting->bTabAnswers			= ($content['bTabAnswers']) ? 1 : 0;
			$meeting->bLIFO					= ($content['bLIFO']) ? 1 : 0;
		}
		$meeting->save();
	}
	
	public function releaseQuestion($id) {
		$q	= new Model_Question();
		$user 		= $this->getUser();
		$question 	= $q->find($id)->current();
		
		if ($question->fk_user == $user->_username) {
			$question->fk_user = new Zend_Db_Expr("NULL");
			$question->save();
			return "OK";
		}
		else {
			return "NOT OK";
		}		
	}
	
	public function saveQA($question) {		
		$user 	= $this->getUser();
		
		$q						= new Model_Question();
		$questionID 			= $question["questionID"];
		$theQuestion 			= $q->find($questionID)->current();		
		$theQuestion->content 	= $question['questionContent'];
		$theQuestion->title 	= $question['questionTitle'];
		$theQuestion->save();
		
		$a					= new Model_Answer();		
		$theAnswer 			= $a->find( $a->getAnswerIdByQuestionId($questionID) )
								->current();
		$answerContent 		= $question['answerContent'];
		 					
		
		if (!$theAnswer) {
			//new answer, we get a new row
			if (strlen($answerContent) != 0) {
				$a->insertRow(
					$questionID
					,$user->_username
					,$question['answerContent']
				);
			}
		}	
		else {
			if (strlen($answerContent) != 0) {
				$theAnswer->content = $question['answerContent'];
				$theAnswer->fk_user	= $user->_username;
				$theAnswer->save();
			}
			else {
				$a->deleteByQuestion($questionID);
			}
			
		}	
		
		return "OK";
	}
	
	public function getMeetingInfo($id){
		$m = new Model_Meeting();
		return $m->getInfoByID($id);
	}
	
	private function getUser() {		
		return Zend_Auth::getInstance()->getIdentity();
	}
	
	public function meeting_postQuestion ($meetingId,$question,$alias,$title,$email) {
		$html_filter = new Zend_Filter_StripTags;
		$html_filter->setAttributesAllowed(array());
		$html_filter->setTagsAllowed(array());		
		
		$date 				= new Zend_Date();
		$q 					= new Model_Question();		
		$row 				= $q->createRow();		
		$row->content 		= $html_filter->filter($question);
		$row->fk_webmeeting	= $meetingId;
		$row->alias			= $html_filter->filter($alias);
		$row->title			= $html_filter->filter($title);
		$row->email			= $html_filter->filter($email);
		$row->dateCreated	= $date->get(Zend_Date::ISO_8601);
		$row->fk_user		= 0;
		$row->bIsAccepted	= 0;
		$row->bIsPublished	= 0;	
		$row->save();		
		return "OK";
	}
	public function meeting_getNumberOfQuestions($id) {	
		$m = new Model_Meeting();
		return $m->getNrAnswers($id);
	}
	
	public function meeting_getQAForMeeting($id,$bDesc) {				
		$q = new Model_Question();		
		return $q->getQAForMeeting($id,$bDesc);
	}
	
	public function meeting_getAcceptedQuestionsNoAnswer($id) {		
		$q = new Model_Question();		
		return $q->getAcceptedQuestionsNoAnswer($id);
	}
	public function isAvalableQuestion($id) {
		$myId 	= $this->getUser();
		$q = new Model_Question();
		
		return $myId->_username; 
	}
	public function getSignature($id) {
		$user	= $this->getUser();	
		$q 		= new Model_UserWebmeetingProperties();
		$tmp 	= $q->getUserSignature($user->_username,$id);
		$tmp	= $tmp[0];		
		return $tmp["signature"];
	}
}
