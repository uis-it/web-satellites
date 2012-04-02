<?php
class CMS_Api
{
	protected function _validateKey($apiKey)
	{
	     // this is for testing only
	     if($apiKey == 'test') {
	         return true;
	     } else {
	         return false;
	     }
	}
	
	public function createPage($apiKey, $name, $headline, $description, $content)
	{
	    if(!$this->_validateKey($apiKey)) {
	        return array('error' => 'invalid api key', 'status' => false);
	    }
		// create a new page item
	    $itemPage = new CMS_Content_Item_Page();
	    $itemPage->name = $name;
	    $itemPage->headline = $headline;
	    $itemPage->description = $description;
	    $itemPage->content = $content;
	
	    // save the content item
	    $itemPage->save();
	
	    // return the page as an array, which Zend_Rest will convert into the XML response
	    return $itemPage->toArray();
	}		
}