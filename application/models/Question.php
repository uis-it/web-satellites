<?php
class Model_Question extends Zend_Db_Table_Abstract
{
	protected $_name = 'question';
	
	public function fetchQuestions()
	{
	    $select = $this->select();
	    return $this->fetchAll($select);
	}
	
	private function dLog ($var, $label = '') {
		$writer = new Zend_Log_Writer_Stream('d:/logs/zendFramework/log.txt');
      	$logger = new Zend_Log($writer);
      	$logger->log(Zend_Debug::dump($var,$label,false),Zend_Log::INFO);		
	}
	
	public function getQAForMeeting($id,$bDesc) {
		$tmp 	= new self();
		$m 		= new Model_Meeting();
		$db = $tmp->_db;	
		$isLIFO = $m->find((Int)$id)->current()->bLIFO;		
		$tmpDB =  $db
					->select()
					->from(
						array('q' => 'question')
						,array(
							'q.content as question'
							,'q.dateCreated as questionDateCreated'
							,'alias'
							,'a.content as answer'
							,'q.title'
							,'u.fullname'
							,'a.id as answerID'
							,'a.dateCreated as answerDateCreated'
							,'u_p.signature as signature'
						)
					)					
					->join (array ('a' => 'answer')
							,'a.fk_question = q.id'
					)		
					->join	(array ('u' => 'users')
							,'a.fk_user = u.id'
					)
					->join	(array ('u_p' => 'user_webmeeting_properties')
							,'u.id = u_p.fk_user'
					)
					->where('q.fk_webmeeting = ?', $id)
					->where('u_p.fk_webmeeting = ?', $id)	
							
		;		
		if ($bDesc) {			
			$tmpDB->order('answerDateCreated desc');		
		}
		else {			
			$tmpDB->order('answerDateCreated asc');
		}
				
		$q = $tmpDB
				->query()
				->fetchAll()
		;
			
				
		return $q;
	}
	public function getQuestionByMeeting($id) {	
		$tmp	= new self();
		$db 	= $tmp->_db;
		$q = $db
				->select()
				->from(
					array('q' => 'question')
					,array(
						'q.content as question'
						,'q.id as qID'
						,'q.dateCreated as qDateCreated'
						,'alias'
						,'a.content as answer'
						,'a.id as aID'
						,'q.title'
						,'u.fullname'
					)
				)					
				->joinLeft (array ('a' => 'answer')
						,'a.fk_question = q.id'
				)		
				->joinLeft(array ('u' => 'users')
						,'a.fk_user = u.id'
				)			
				->where('fk_webmeeting = ?', $id)	
				->order('q.id desc')					
				->query()
				->fetchAll()			 
			;				
		return $q;	
	}
	public function getAcceptedQuestionsNoAnswer($id) {
		$tmp = new self();
		$db = $tmp->_db;	
		$q = $db
				->select()
				->from(
					array('q' => 'question')
					,array(
						'q.content as question'
						,'alias'
						,'a.content as answer'
						,'a.id as answerID'
						,'q.bIsAccepted'
						,'q.dateCreated as theDate'
					)
				)					
				->joinLeft (array ('a' => 'answer')
						,'a.fk_question = q.id'
				)				
				->where('fk_webmeeting = ?', $id)
				->where('a.id is NULL','')		
				->where('q.bIsAccepted = ?',1)				
				->query()
				->fetchAll()			 
			;			
		return $q;
	}
}