<?php
class TranslationController extends Zend_Controller_Action
{
	
 	public function init()
    {
        /* Initialize action controller here */		
    }
public function translationAction()
    {
    	$dao = new Model_Translation();
		$this->view->sKeys = $dao->getList("BOKMÅL"); 
		//d($dao->getList("BOKMÅL")); die();	
    }
 public function saveAction()
    {
    	$dao = new Model_Translation();
    	$a = array();
    	$arr = array();
		foreach ($_POST as $key => $value){
			$tmp = explode("::", $key);
			
			$id1 	= $tmp[0];
			$id2 	= $tmp[1];
			if($tmp[2] == "NO")
				$lang	= "BOKMÅL";
			else if($tmp[2] == "EN")
				$lang	= 'ENGELSK';
			/*$a[0] = $id1;
			$a[1] = $id2;
			
			$a[2] =$lang;
			$a[3] =$value;
			*/if (is_numeric($id2)) 
			{
				$dao->updateLEvel2CompetenceArea(
							$id1
							,$id2
							,$value
							,$lang
				);				
			}
			else 
			{								
				$dao->updateMainCompetenceArea(
							$id1
							,$value
							,$lang
				);				
			}
		}
		$this->_redirect("/translation/translation");
			/*$dao->updateMainCompetenceArea(
							$item->id1
							,$item->value
							,$lang
			);*/	
			
			
			//array_push($arr,$a);
		//}
		/*echo count($arr);
		d($arr);
					
		die();*/
}

}