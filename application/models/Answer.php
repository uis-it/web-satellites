<?php 

class Model_Answer extends Zend_Db_Table_Abstract
{
	protected $_name = 'answer';
	
	public function fetchAnswers()
	{
	    $select = $this->select();
	    return $this->fetchAll($select);
	}
	
	public function deleteByQuestion($id) {
	  $where = $this->getAdapter()->quoteInto('fk_question = ?', $id);
      $this->delete($where);
	}
	
	public function insertRow($fk_question,$fk_user,$content) {
		$date 				= new Zend_Date();
		$row 				= $this->createRow();
		
		$row->fk_question 	= $fk_question;
		$row->fk_user 		= $fk_user;		
		$row->dateCreated	= $date->get(Zend_Date::ISO_8601);
		$row->content		= $content; 		
	    return  $row->save();
	}
	
	public function getByQuestion($id) {		
		return $this
			->select()
			->from(
				"answer"
			,	"*"
			)
			->where("fk_question = ?",$id)
			->query()
			->fetchAll()
		;		           
	}
	public function getAnswerIdByQuestionId($id) {
		$q = $this
			->select()
			->from(
				"answer"
			,	"id"
			)
			->where("fk_question = ?",$id)
			->query()
			->fetchAll()
		;
		return $q[0]['id'];		
	}
}