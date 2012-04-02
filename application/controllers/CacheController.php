<?php

class CacheController extends Zend_Controller_Action
{
	public $pathToCache = 'D:/Cache/';
	public function init()
    {
    	$options = Zend_Registry::get('OPTIONS');
    	$this->pathToCache = $options['cache']['basedir'];
    }
	
    public function listAction() {
    	$this->view->listDirectories = $this->getAllCahedDirectories();    	
    }
    public function deleteAction() {    	
    	$this->recursive_remove_directory($this->pathToCache.$this->_request->getParam('path'));
    	$this->_redirect('/cache/list/');
    	
    }
    
    
    
	private function getAllCahedDirectories(){		
		$returnArray = array();
		$dirname = $this->pathToCache;
		if (is_dir($dirname)){			
			$dir_handle = opendir($dirname);
			if (!$dir_handle){
				echo 'Not valid directory: '.$dirname.'<br />';
				return false;
			}
			while($file = readdir($dir_handle)) {
				if ($file != "." && $file != "..") {
					$returnArray[] = $file;
				}
			}
			
			closedir($dir_handle);
			return $returnArray;	
		}
	}
	
	// ------------ lixlpixel recursive PHP functions -------------
	// recursive_remove_directory( directory to delete, empty )
	// expects path to directory and optional TRUE / FALSE to empty
	// of course PHP has to have the rights to delete the directory
	// you specify and all files and folders inside the directory
	// ------------------------------------------------------------

	// to use this function to totally remove a directory, write:
	// recursive_remove_directory('path/to/directory/to/delete');

	// to use this function to empty a directory, write:
	// recursive_remove_directory('path/to/full_directory',TRUE);

	private function recursive_remove_directory($directory, $empty=FALSE)
	{
		
		// if the path has a slash at the end we remove it here
		if(substr($directory,-1) == '/')
		{
			$directory = substr($directory,0,-1);
		}

		// if the path is not valid or is not a directory ...
		if(!file_exists($directory) || !is_dir($directory))
		{
			// ... we return false and exit the function
			return FALSE;

		// ... if the path is not readable
		}elseif(!is_readable($directory))
		{
			// ... we return false and exit the function
			return FALSE;

		// ... else if the path is readable
		}else{

			// we open the directory
			$handle = opendir($directory);

			// and scan through the items inside
			while (FALSE !== ($item = readdir($handle)))
			{
				// if the filepointer is not the current directory
				// or the parent directory
				if($item != '.' && $item != '..')
				{
					// we build the new path to delete
					$path = $directory.'/'.$item;

					// if the new path is a directory
					if(is_dir($path)) 
					{
						// we call this function with the new path
						$this->recursive_remove_directory($path);

					// if the new path is a file
					}else{
						// we remove the file
						unlink($path);
					}
				}
			}
			// close the directory
			closedir($handle);

			// if the option to empty is not set to true
			if($empty == FALSE)
			{
				// try to delete the now empty directory
				if(!rmdir($directory))
				{
					// return false if not possible
					return FALSE;
				}
			}
			// return success
			return TRUE;
		}
	}
	// ------------------------------------------------------------
	
	
}