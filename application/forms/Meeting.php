<?php
class Form_Meeting extends Zend_Dojo_Form
{
    public function init()
    {
    	Zend_Dojo::enableForm($this);
    	
    	
    	$this->setAttrib('enctype', 'multipart/form-data');

    	$this->addElement(
			'hidden'
			,'id'			
		);
    	
		$this->addElement(
			'TextBox'
		    ,'meetingName'
		    ,array(
		        'value'			=> ''
		        ,'label'      	=> 'Meeting name:'
		        ,'trim'       	=> true
		        ,'required'		=> true
		    )
		);
		$this->addElement(
			'TextBox'
		    ,'photo_thumbnail_url'
		    ,array(
		        'value'			=> ''
		        ,'label'      	=> 'Photo thumbnail:'
		        ,'trim'       	=> true
		        ,'required'		=> false
		    )
		);
		$this->addElement(
			'TextBox'
		    ,'photo_url'
		    ,array(
		        'value'			=> ''
		        ,'label'      	=> 'Photo:'
		        ,'trim'       	=> true
		        ,'required'		=> false
		    )
		);
    	$this->addElement(
		    'DateTextBox'
		    ,'live_from_day'
		    ,array(
		        'label'				=> 'Live from day:'
		        ,'required'      	=> true
		        ,'invalidMessage' 	=> 'Invalid date specified.'
		        ,'formatLength'   	=> 'short'
		        ,'PromptMessage'	=> 'Please select a date you wish for this article to be posted.'
		        
		    )
		);	
		$this->addElement(
          'TimeTextBox',
          'live_from_hour',
          array(          	  
              'label'              => 'Live from hour',
              'required'           => true,
              'visibleRange'       => 'T04:00:00',
              'visibleIncrement'   => 'T00:30:00',
              'clickableIncrement' => 'T00:30:00',
          	
          )
      	);
		$this->addElement(
		    'DateTextBox'
		    ,'live_to_day'
		    ,array(
		        'label'				=> 'Live to day:'
		        ,'required'      	=> true
		        ,'invalidMessage' 	=> 'Invalid date specified.'
		        ,'formatLength'   	=> 'short'
		    )
		);	
		$this->addElement(
          'TimeTextBox',
          'live_to_hour',
          array(
              'label'              => 'Live to hour',
              'required'           => true,
              'visibleRange'       => 'T04:00:00',
              'visibleIncrement'   => 'T00:30:00',
              'clickableIncrement' => 'T00:30:00',
          )
      	);		
		$this->addElement(
		    'SimpleTextarea'
		    ,'description_small'
		    ,array(
		        'value'    	=> ''
		        ,'label'   	=> 'Small description:'
		        ,'cols'		=> '100'
		        ,'rows'		=> '6'
		    )
		);
		
		$this->addElement(
		    'editor'
		    ,'description'		    
		    ,array(
		        'value'    	=> ''
		        ,'label'   	=> 'Description:'
		        ,'cols'		=> '130'
		        ,'rows'		=> '10'
		        ,'dojoType'	=> 'dijit.Editor'		        
		        ,'plugins'     => array(	
		        					'undo'	
		        					,'redo'        					
		        					,'copy'
		        					,'paste'
		        					,'|'
		        					,'bold'
		        					,'italic'
		        					,'|'
		        					,'createLink'
		        					,'unlink'
		        					,'|'
		        					,'fontName'
		        					,'fontSize'
		        					,'formatBlock'
		        				)
		    )
		);
		$this->addElement(
		    'SimpleTextarea'
		    ,'description_internal'
		    ,array(
		        'value'    	=> ''
		        ,'label'   	=> 'Internal information (users etc):'
		        ,'cols'		=> '100'
		        ,'rows'		=> '6'
		    )
		);        
		$this->addElement(
			'CheckBox',
          	'bShow',
          	array(
	              'label'          => 'Is active:',
	              'checkedValue'   => 'Yes',
	              'uncheckedValue' => 'No',
	              'checked'        => true,
         	)
      	);
		
        $this->addElement(
		    'SubmitButton',
		    'submit',
		    array(
		        'required'   => false,
		        'ignore'     => true,
		        'label'      => 'Submit Button!',
		    )
		);

        
    }   
}