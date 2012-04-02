<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	protected function _initAutoload()
	{
	    // Add autoloader empty namespace
	    $autoLoader = Zend_Loader_Autoloader::getInstance();
	    $autoLoader->registerNamespace('CMS_');	    
	    $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
	        'basePath'      => APPLICATION_PATH,
	        'namespace'     => '',
	        'resourceTypes' => array(
	            'form' => array(
	                'path'      => 'forms/'
	                ,'namespace' => 'Form_'
	            )
	            ,'model' => array(
	                'path'      => 'models/'
	                ,'namespace' => 'Model_'
	            )           
	        )
	    ));
	    // Return it so that it can be stored by the bootstrap
	    return $autoLoader;
	}
	
	protected function _initOptions($resource = null) {
		$options = $this->getOptions();
		Zend_Registry::set('OPTIONS', $options);
		
		return $options;
	}

	protected function _initView()
	{
	    // Initialize view
	    $view = new Zend_View();
	    $view->doctype('XHTML1_STRICT');
	    $view->headTitle('UiS WebSatellites Administrator');
	    $view->skin = 'blues';
		
	    $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
	    $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
	  	$viewRenderer->setView($view);

    	Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
	    
	    /*
		 * setup empty array for javascripts to include,
		 * so each controller will add the ones that they
		 * need for the views.
		 */ 
    	$view->aJsToInclude = array();
    	$view->aCssToInclude = array();
    	
		//Return it, so that it can be stored by the bootstrap
    	return $view;
	}
	
	protected function _initMenus ()
	{
	    $view = $this->getResource('view');

	    $auth = Zend_Auth::getInstance();
	   	
	   	$view->topMenu	= array();
	   	if ($auth->hasIdentity()) {
 		    $identity 	= $auth->getIdentity();
 	   		$role = $identity->_role;

 	   		$cacheEnabled = $this->isEnabled('cache');
 	   		
		    if ($role == 'administrator') {
		    	if ($cacheEnabled) {		    	
			    	$tmp = array(
				    	'txt' 	=> 'Clear cache'
				    	,'url' 	=> '/cache/list/'
				    );
			    	array_push($view->topMenu,$tmp);
		    	}
		    
			    $tmp = array(
			    	'txt' 	=> 'Nettmøter'
			    	,'url' 	=> '/meeting/list/'
			    );
			    array_push($view->topMenu,$tmp);
			    
			    $tmp = array(
			    	'txt' 	=> 'Forum'
			    	,'url' 	=> '/forum/admin/'
			    );
			    array_push($view->topMenu,$tmp);

			    if ($this->isEnabled('translation')) {
				    $tmp = array(
				    	'txt' 	=> 'Translation'
				    	,'url' 	=> '/translation/translation/'
				    );
				    array_push($view->topMenu,$tmp);
			    }			    
		    
		    } else if ($role == 'user') {
		    	if ($cacheEnabled) {
			    	if (in_array('clearcache',$identity->access)) {
				    	$tmp = array(
					    	'txt' 	=> 'Clear cache'
					    	,'url' 	=> '/cache/list/'
					    );
				    	array_push($view->topMenu,$tmp);
			    	}
		    	}	
		    	if (in_array('webmeeting',$identity->access)) {
			    	$tmp = array(
				    	'txt' 	=> 'Meetings'
				    	,'url' 	=> '/meeting/list/'
				    );
			    	array_push($view->topMenu,$tmp);
		    	}	
		    		    	
		    	if (in_array('forum',$identity->access)) {
			    	$tmp = array(
				    	'txt' 	=> 'Meetings'
				    	,'url' 	=> '/forum/admin/'
				    );
			    	array_push($view->topMenu,$tmp);
		    	}		    	
		    }			        
	   	}	   	
	}
	
	protected function _initTranslate() {
		$view = $this->getResource('view');
		$options = $this->getOptions();
		$options = $options['resources']['translate'];
        $adapter = $options['adapter'];     
        $defaultTranslation = $options['default']['file'];
        
        $defaultLocale = $options['default']['locale'];
        
        $locale = new Zend_Locale($defaultLocale);
		Zend_Registry::set('Zend_Locale', $locale);
        
		$translate = new Zend_Translate($adapter, $defaultTranslation, $defaultLocale);        
        foreach ($options['translation'] as $locale => $translation) {        	       
            $translate->addTranslation($translation, $locale);
        }
        Zend_Registry::set('Zend_Translate', $translate);  
		$view->locale = $defaultLocale;		
	}

	private function isEnabled($option) {
		$opt = $this->getOption($option);
			
		if (isset($opt)) {
			return array_key_exists('enable', $opt) && $opt['enable'];
		}
		return false;
	}			
}
	


