<?php

class Model_Forum extends Zend_Db_Table_Abstract
{
	protected $_name = 'uis_forum';
	protected $_schema = 'forum2';
	
	public  function  getNewTopics(){
		$forum	= new self();
		$db 	= $forum->_db;
		$select = $db
					->select()
					->from(array('topic' => 'forum2.uis_forum'), array(
					'topic.innlegg_ID' ,
					'topic.overskrift',
					'topic.dato',
					'topic.fornavn',
					'topic.sak',
					'topic.etternavn'))
            ->where('topic.godkjent = 0')
            ->order('dato DESC')
			->query()
			->fetchAll();		
		return  $select;
	}
	
	public  function  getNewComments(){
		$forum = new self();
		$db = $forum->_db;
		$select = $db
					->select()
					->from(array('c' => 'forum2.uis_forum_comment'), array(
					'c.id',
					'c.fk_godkjentID',
					'c.authorName',
					'c.dateSent',
					'c.title',
					'g.overskrift'))
					->joinInner(array('g'=> 'forum2.uis_forum_godkjent'), 'g.godkjent_ID = c.fk_godkjentID')
					->where('bApproved = 9')
					->order('dateSent desc')
					->query()
					->fetchAll();		
		return  $select;
	}
	
	public function getDashboardThread($id) {		
		$forum	= new self();
		$select 	= $forum
						->select()
						->from("forum2.uis_forum","*")	
						->where("innlegg_ID = ?",$id)									
						->query()
						->fetchAll();		
		return $select;	
	}
	
	public function updateDashboardThread($thread){
		$where = 'innlegg_ID='.$thread['innlegg_ID'];
		$forum	= new self();
		$db = $forum->_db;
		$insert = $db
				->insert('forum2.uis_forum_godkjent', $thread);
		$n  = $db
			->update('forum2.uis_forum', array('godkjent' => $thread['godkjent']), $where);
	}

	public function  getAcceptedOrRejectedList($godkjent){
		$forum	= new self();
		$db 	= $forum->_db;		
		$select = $db
					->select()
					->from(array('topic' => 'forum2.uis_forum_godkjent')
					, array(
					'topic.godkjent_ID',
					'topic.overskrift',
					'topic.dato',
					'topic.fornavn',
					'topic.sak',
					'topic.etternavn',
					'topic.godkjent'
					,'commentsNew' => ' ( select count(id) 
										  from forum2.uis_forum_comment 
										  where fk_godkjentID = topic.godkjent_ID and bApproved = 9)'
										  
					,'commentsApproved' => ' ( select count(id) 
											  from forum2.uis_forum_comment 
											  where fk_godkjentID = topic.godkjent_ID and bApproved = 1)'
											  
					,'commentsRejected' => ' ( select count(id) 
											  from forum2.uis_forum_comment 
											  where fk_godkjentID = topic.godkjent_ID and bApproved = 0)'
					))
					->joinLeft(array('visits'=>'forum2.uis_forum_visits'),
						'topic.godkjent_ID = visits.fk_forum',
						array('visits' => 'count')					
					)
           ->where('topic.godkjent = ?',$godkjent)
           ->order('godkjent_ID DESC')
		   ->query()
		   ->fetchAll();
		return  $select;
	}
	
	public function getThread($godkjent_ID){
		$forum	= new self();
		$db 	= $forum->_db;
		$select = $db
				->select()
				->from('forum2.uis_forum_godkjent', "*")
				->where("godkjent_ID = ?" ,$godkjent_ID)
				->query()
				->fetchAll();				
		return $select;		
	}
	
	public function updateThread($post){
		$where = 'godkjent_ID='.$post['godkjent_ID'];
		$forum	= new self();
		$db = $forum->_db;
		$n  = $db
			->update('forum2.uis_forum_godkjent', $post, $where);	
		}
		
	public function getComments($theID, $theCommentType){	
		$result = new stdClass();
		$forum	= new self();
		$db 	= $forum->_db;
		//Fetching the comments
		$select = $db
				->select()
				->from('forum2.uis_forum_comment', array(
					'id',
					'fk_godkjentID',
					'bApproved',
					'authorName',
					'title',
					'commentContent',
					'authorEmail',
					'dateSent',
					'authorWeb'))
				->where("fk_godkjentID = ?" ,$theID)
				->where("bApproved = ?", $theCommentType) //comment type can be 9->New , 1->rejected , 0->accepted
				->query()
				->fetchAll();
		$result->commentList = $select;
		
		//Fetching the Topic Title
		$forum	= new self();
		$db 	= $forum->_db;
		$select = $db
				->select()
				->from('forum2.uis_forum_godkjent', array('overskrift'))
				->where("godkjent_ID = ?" ,$theID)
				->query()
				->fetchAll();
		$result->topicTitle = $select[0]['overskrift'];
		
		switch($theCommentType){
				case '9':{
					$result->lableAcceptReject = "NEW";		
					$result->theTitle = "Ny kommentarer for ";
					break;
					}
				case '0' : {
					$result->extraCheckedRejected = 'checked="checked"';
					$result->extraCheckedAccepted = '';
					$result->lableAcceptReject = "Avvist";
					$result->theTitle = "Avvist kommentarer for ";
					break;
					}
				case '1' : {
					$result->extraCheckedAccepted = 'checked="checked"';
					$result->extraCheckedRejected = '';
					$result->lableAcceptReject = "Godkjent";
					$result->theTitle = "Godkjent kommentarer for ";
					break;
					}
			}				
		return $result;
	}
	
	public function updateComment($comment){
	    $where = 'id='.$comment['id'];
		$forum	= new self();
		$db = $forum->_db;
		$n  = $db
			->update('forum2.uis_forum_comment', $comment, $where);	
	}	
}