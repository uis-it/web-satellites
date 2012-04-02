<?php
class Model_Meeting extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'webmeeting';
	
	public static function getMeetings()
	{
	    $meeting = new self();	    
	    $select = $meeting->select();
	    $select->order(array('id'));
	    return $meeting->fetchAll($select);
	}
	
	public static function getMeetingsFromDate($from) {
		$hour	= 0;
		$min 	= 0;
		$year	= substr($from,6,4);
		$month	= substr($from,3,2);
		$day	= substr($from,0,2);		
		$fromDate = $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $min . ':00';
			
		$meeting	= new self();		
		$tmp = $meeting
				->select()
				->from(
					"webmeeting"
				,	"*"
				)
				->where("live_from > ?",$fromDate)
				->order("live_from")
				->query()
				->fetchAll()
		;		
		return $tmp;	
	}
	
	public function getMeetingById($id) {		
		$meeting	= new self();
		$qMeeting 	= $meeting
						->select()
						->from(
							"webmeeting"
						,	"*"
						)
						->where("id = ?",$id)
						->where("bIsDeleted <> 1")						
						->query()
						->fetchAll()
					;		
		return $qMeeting;	
	}
	
	
	
	public function fetchMeetings()
	{
	    $select = $this->select();
	    return $this->fetchAll($select);
	}
	public function create(
						$title
						,$description_small
						,$description
						,$description_internal
						,$bShow
						,$live_from
						,$live_to
						,$photo_thumbnail_url
						,$photo_url
					)
	{
	    $row 						= $this->createRow();
	    $row->title 				= $title;
	    $row->description_small		= $description_small;
	    $row->description			= $description;
	    $row->description_internal 	= $description_internal;
	    $row->bShow 				= $bShow;
	    $row->live_from				= $live_from;
	    $row->live_to				= $live_to;	    
	    $row->photo_thumbnail_url	= $photo_thumbnail_url;
	    $row->photo_url				= $photo_url;
	    return  $row->save();
	}	
	public function updateM(
							$id
							,$title
							,$description_small
							,$description	
							,$description_internal						
							,$bShow
							,$live_from
							,$live_to
							,$photo_thumbnail_url
							,$photo_url
					) 
	{
		$row = $this->find((Int)$id)->current();
		if($row) {
			$row->title 				= $title;
			$row->description_small		= $description_small;
		    $row->description			= $description;
		    $row->description_internal	= $description_internal;
		    $row->bShow 				= $bShow;
		    $row->live_from				= $live_from;
		    $row->live_to				= $live_to;
		    $row->photo_thumbnail_url	= $photo_thumbnail_url;
		    $row->photo_url				= $photo_url;
			return  $row->save();
		} else{
	        throw new Zend_Exception("Meeting update failed.  Meeting not found!");
	    }
	} 	
	public function getNrAnswers ($id) {
		$tmp	= new self();
		$db 	= $tmp->_db;
		$q = $db
				->select()
				->from(
					array('q' => 'question')
					,"id"
				)
				->join(
						array ('a' => 'answer')
						,'a.fk_question = q.id'
				)	
				->where("fk_webmeeting = ?",$id)
				->query()
				->fetchAll()
		;	
		return count($q);
	}
	public function getInfoByID($id) {	
		$ret 	= Array(); 
		$tmp	= new self();
		$db 	= $tmp->_db;		
		
		$meetingInfo = $this->getMeetingById($id);
		$meetingInfo = $meetingInfo[0];
		$q = $db
				->select()
				->from(
					array('q' => 'question')
					,"count(id) as tot"
				)				
				->where("fk_webmeeting = ?",$id)
				->query()
				->fetchAll()
		;	
		$tmp = $q[0];
		$ret["totalQ"] = $tmp["tot"];
		
		$q = $db
				->select()
				->from(
					array('q' => 'question')
					,"q.id"
				)
				->join(
						array ('a' => 'answer')
						,'a.fk_question = q.id'
				)	
				->where("fk_webmeeting = ?",$id)
				->query()
				->fetchAll()
		;			
		
		$ret["totalQWithAnswer"]		= count($q);
		$ret['title'] 					= $meetingInfo['title'];
		$ret['welcomeText']				= $meetingInfo['welcomeText'];
		$ret['goodbyeText']				= $meetingInfo['goodbyeText'];
		$ret['bIsLive']					= $meetingInfo['bIsLive'];
		$ret['bTabAcceptedQuestions']	= $meetingInfo['bTabAcceptedQuestions'];
		$ret['bAcceptQuestion']			= $meetingInfo['bAcceptQuestion'];
		$ret['bTabAnswers']				= $meetingInfo['bTabAnswers'];
		$ret['bLIFO']					= $meetingInfo['bLIFO'];
		$tmp							= $meetingInfo['description_internal'];		
		$tmp = nl2br  ($tmp);							
		$ret['contactInfo']				= $tmp;
		return $ret;		
	}	
}