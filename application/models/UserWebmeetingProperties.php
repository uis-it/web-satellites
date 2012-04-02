<?php 
class Model_UserWebmeetingProperties extends Zend_Db_Table_Abstract
{
	protected $_name = 'user_webmeeting_properties';

	public function insertRow($fk_user, $fk_webmeeting, $signature) {
		$row 				= $this->createRow();
		$row->fk_user	 	= $fk_user;				
		$row->fk_webmeeting	= $fk_webmeeting;
		$row->signature 	= $signature;
	    return  $row->save();
	}
	
	public function getUserSignature($userId,$webmeetingId) {
        $tmp	= new self();
		$db 	= $tmp->_db;
		$q = $db
				->select()
				->from(
					array('q' => 'user_webmeeting_properties')
					,"*"
				)
				->where("fk_webmeeting = ?",$webmeetingId)
				->where("fk_user = ?",$userId)
				->query()
				->fetchAll()
		;			
		return $q;		
	}
	public function updateSignature($userId,$webmeetingId,$signature) {
		$tmp		= new self();
		$db 		= $tmp->_db;
		$where[] 	= "fk_webmeeting = ".$webmeetingId;
		$where[] 	= "fk_user = ".$userId;
		$data 		= array (
						'signature' => $signature
					); 
		$q = $db->update(
					'user_webmeeting_properties'
					, $data
					, $where
				);			
		return $q;		
	}
}