<?php 
class CMS_Forum {

	public function meeting_getForum() {
		$m = new Model_Forum();		
		return $m->create("test desde js","y si sale!!",true);
	}
	
	public function fetchForums()
	{
	    $select = $this->select();
	    return $this->fetchAll($select);
	}
	

}