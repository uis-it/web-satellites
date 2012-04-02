var Meeting =  {
	init: function(id) {
		this.id 			= id;
		this.myApi 			= jQuery.Zend.jsonrpc({url: '/json.php'});
		this.questionList2 	= jQuery("#leftPane");
		this.questionList 	= jQuery("#listQuestions");
		this.mInfo	= jQuery("#MeetingTopInfo");
		this.listQuestions 	= "";
		
		this.currentOpen	= 0;
		this.questionContent= jQuery("#questionContent");
		this.questionAlias 	= jQuery("#questionAlias");
		this.questionDate	= jQuery("#questionDate");
		this.answerContent 	= jQuery("#answerContent");
		this.questionTitle 	= jQuery("#questionTitle");
		this.saveTrigger	= jQuery("#saveTrigger");		
		this.formDialog		= jQuery('#theFormDialog');
		this.formContent	= jQuery('#theEditor');
		this.qBlockedDialog	= jQuery('#theLockedDialog');
		
		this.welcomeEditTrigger	= "";
		this.goodbyeEditTrigger	= "";
		this.statusEditTrigger	= "";
		this.signatureEditTrigger	= "";
		
		jQuery.metadata.setType("attr", "data");	
		this.connectDialogBox();
		this.connectLockedDialogBox();
	}
	,connectTriggers : function() {
		Meeting.welcomeEditTrigger 		= jQuery("#welcomeEditTrigger");
		Meeting.goodbyeEditTrigger 		= jQuery("#goodbyeEditTrigger");
		Meeting.statusEditTrigger 		= jQuery("#statusEditTrigger");
		Meeting.signatureEditTrigger 	= jQuery("#signatureEditTrigger");
		
		Meeting.welcomeEditTrigger.click(
			function() {
				var m = Meeting.getInfo();				
				Meeting.formContent.html("");				
				var theContent = new Object();
				theContent.theElement 		= "Welcome Text";
				theContent.theContent 		= m.welcomeText;
				theContent.theContentField 	= "welcomeText"; 
				jQuery("#blockEditContent")
					.tmpl( theContent )
					.appendTo( Meeting.formContent )
				;
				jQuery('#theEditor').dialog('open');				
			}
		);
		Meeting.goodbyeEditTrigger.click(
			function() {
				var m = Meeting.getInfo();					
				Meeting.formContent.html("");				
				var theContent = new Object();
				theContent.theElement 		= "Goodbye Text";
				theContent.theContent 		= m.goodbyeText;
				theContent.theContentField 	= "goodbyeText"; 
				jQuery("#blockEditContent")
					.tmpl( theContent )
					.appendTo( Meeting.formContent )
				;
				jQuery('#theEditor').dialog('open');				
			}
		);
		Meeting.signatureEditTrigger.click(
				function() {
					var m = Meeting.getSignature();					
					Meeting.formContent.html("");				
					var theContent = new Object();
					theContent.theElement 		= "Signature";
					theContent.theContent 		= m;
					theContent.theContentField 	= "signatureText"; 
					jQuery("#blockEditContent")
					.tmpl( theContent )
					.appendTo( Meeting.formContent )
					;
					jQuery('#theEditor').dialog('open');				
				}
		);
		Meeting.statusEditTrigger.click(
			function () {
				var display = jQuery('#theLiveDialog');
				display.html("");
				var m 						= Meeting.getInfo();					
				var info 					= new Object();				
				info.bIsLive 				= m.bIsLive;
				info.bTabAcceptedQuestions	= m.bTabAcceptedQuestions;
				info.bAcceptQuestion		= m.bAcceptQuestion;
				info.bTabAnswers			= m.bTabAnswers;
				info.bLIFO					= m.bLIFO;
				jQuery("#blockLiveMeeting")
					.tmpl( info )
					.appendTo( display )
				;
				
				display.bind( "dialogopen", function(event, ui) {
					Meeting.bindLiveTriggers();
				});								
				display.dialog('open');
			}
		);
	}	
	,saveContent : function() {				
		Meeting.myApi.meeting_saveContent(
			Meeting.id
			,jQuery("#theContent").val()
			,jQuery("#theContentField").val()
		);
	}
	,saveStatus : function() {
		var status 						= new Object();
		status.bIsLive 					= jQuery("#bIsLive:checked").val() == "on";
		status.bAcceptQuestion 			= jQuery("#bAcceptQuestion:checked").val() == "on";
		status.bTabAcceptedQuestions	= jQuery("#bTabAcceptedQuestions:checked").val() == "on";
		status.bTabAnswers 				= jQuery("#bTabAnswers:checked").val() == "on";	
		status.bLIFO 					= jQuery("#bLIFO:checked").val() == "on";	
		
		Meeting.myApi.meeting_saveContent(
				Meeting.id
				,status
				,'status'				
			);
		
	}
	,connectDialogBox : function() {
		jQuery('#theFormDialog').dialog({			
			autoOpen: 	false			
			,width: 	600
			,draggable: true
			,show: 		'blind'
			,hide: 		'explode'
			,modal: 	true
			,close: 	function(event,ui) { Meeting.unlock(); }
			,buttons: {				 
				"Cancel": function() { 
					$(this).dialog("close"); 
					Meeting.unlock();
				}
				, "Save": function() { 
					Meeting.saveQA(); 
				} 
			}
		});		
		jQuery('#theEditor').dialog({
			autoOpen: false
			,width: 600
			,draggable: true
			,show: 'blind'
			,hide: 'explode'
			,modal: true
			,buttons: {				 
				"Cancel": function() { 
					$(this).dialog("close"); 					
				}
				, "Save": function() { 
					Meeting.saveContent(); 
					$(this).dialog("close"); 	
				} 
			}
		});		
		jQuery('#theLiveDialog').dialog({
			autoOpen: false
			,width: 600
			,draggable: true
			,show: 'blind'
			,hide: 'explode'
			,modal: true	
			,buttons: {				 
				"Cancel": function() { 
					$(this).dialog("close"); 					
				}
				, "Save": function() { 
					Meeting.saveStatus(); 
					$(this).dialog("close"); 	
				} 
			}
		});	
	}
	,connectLockedDialogBox : function () {
		jQuery('#theLockedDialog').dialog({
			autoOpen: false
			,width: 600
			,draggable: true
			,show: 'blind'
			,hide: 'explode'
			,modal: true
			,buttons: {				 
				"Cancel": function() { 
					$(this).dialog("close"); 
					Meeting.unlock();
				}				
			}
		});		
	}
	,getQuestions : function() {
		Meeting.listQuestions = Meeting.myApi.getQuestionsByMeeting(Meeting.id);		
	}	
	,getInfo : function(){
		return Meeting.myApi.getMeetingInfo(Meeting.id);
	}
	,getSignature : function() {
		return Meeting.myApi.getSignature(Meeting.id);
	}
	
	,refreshQuestionList : function (recall) {
		Meeting.questionList.html("");
		Meeting.mInfo.html("");
		
		var meetingInfo = Meeting.getInfo();
		
		jQuery("#blockInfo")
			.tmpl( meetingInfo )
			.appendTo( Meeting.mInfo );		
		jQuery("#meetingInfoContact").html(
				Meeting.fixLTGT(jQuery("#meetingInfoContact").html())
		);
		
		Meeting.getQuestions();				
		for (var i = 0;i < Meeting.listQuestions.length;i++) {
			var node = Meeting.listQuestions[i];
			var tmpD 	= node.qDateCreated.split(' ');
			var tmpH	=	tmpD[1];
			tmpD 		= tmpD[0];
			tmpD		= tmpD.split('-');
			tmpH = tmpH.split(':');
			node.dateCreated = tmpD[2]+'.'+tmpD[1]+'.'+tmpD[0]+' '+tmpH[0]+':'+tmpH[1];
			node.odd = "";				
			if (i % 2 == 0) {
				node.odd = " odd";
			}
			jQuery("#blockQuestion")
				.tmpl( node )
				.appendTo( Meeting.questionList );
		}
		Meeting._conectListFunctionality();
		Meeting.connectTriggers();
		Meeting.fixReturnChar();		
		if (recall) {
			setTimeout("Meeting.refreshQuestionList(true)", 50000);
		}
	}
	,fixReturnChar : function() {
		jQuery(".question-content").each(function(){
			jQuery(this).html(
				Meeting.slashN2BR(jQuery(this).html())
			);
		});
	}
	,_conectListFunctionality : function() {
		jQuery(".question").hover(
				function(){							
					jQuery(this).addClass("hover");				
				}
				,function() {
					jQuery(this).removeClass("hover");					
				}
		);
		jQuery(".question-dashboard").click(function(e) {
			e.stopPropagation();			
		});
		jQuery(".question").click(function(){ 
			Meeting.formDialog.html("");	
			
			var jqThis 						= jQuery(this);
			var data 						= jQuery.metadata.get(this);
			
			var _currentQuestion 			= Meeting.myApi.getQuestion(data.id);
			
			var q 							= _currentQuestion.question[0];
			
			var isAvalaible = false;
			if ( _currentQuestion.bIsBlocked ) {
				if (_currentQuestion.bIsMine) {
					isAvalaible = true;
				}				
			}
			else {
				isAvalaible = true;
			}
			
			if (isAvalaible) {
				Meeting.captureQuestion(data.id);			
			
				var theQuestion 				= new Object(); //_currentQuestion.question;
				
				var tmpD 						= q.dateCreated.split(' ');
				var tmpH						= tmpD[1];
				tmpD 							= tmpD[0];
				tmpD							= tmpD.split('-');
				tmpH 							= tmpH.split(':');
				theQuestion.qDateCreated 		= tmpD[2]+'.'+tmpD[1]+'.'+tmpD[0]+' '+tmpH[0]+':'+tmpH[1];
				
				theQuestion.qContent 		= q.content;
				theQuestion.qID 			= q.id;
				Meeting.questionToEditID	= q.id;
				theQuestion.qbIsAccepted	= q.bIsAccepted;
				theQuestion.qbIsPublished	= q.bIsPublished;
				theQuestion.qfk_user		= q.fk_user;
				theQuestion.qfk_webmeeting	= q.fk_webmeeting;
				theQuestion.qTitle			= q.title;
				theQuestion.qAlias			= q.alias;
				theQuestion.qEmail			= q.email;
	
				JSON.stringify(theQuestion);
				
				theQuestion.answerContent		= _currentQuestion.answerContent;
				theQuestion.bIsMine				= _currentQuestion.bIsMine;		
				theQuestion.bIsBlocked			= _currentQuestion.bIsBlocked;		
				
				jQuery("#blockEditQAForm")
					.tmpl( theQuestion )
					.appendTo( Meeting.formDialog );
				Meeting.formDialog.dialog('open');
			}
			else {
				Meeting.qBlockedDialog.html("You don't owe this question, the owner is: "+_currentQuestion.user_name);		
				Meeting.qBlockedDialog.dialog('open');
			}			
		});
	}
	,unlock : function() {
		Meeting.myApi.releaseQuestion(Meeting.questionToEditID);		
	}
	,saveQA : function() {		
		var _theQuestion = new Object();
		_theQuestion.questionContent	= jQuery("#questionContent").val();
		_theQuestion.answerContent 		= jQuery("#answerContent").val();
		_theQuestion.questionID 		= Meeting.questionToEditID;
		_theQuestion.questionTitle 		= jQuery("#questionTitle").val();		
		Meeting.myApi.saveQA(_theQuestion);
		Meeting.formDialog.dialog('close');
		Meeting.refreshQuestionList(false);		
		Meeting.releaseQuestion(Meeting.questionToEditID);
	}
	,conectListFunctionality : function(){	
		jQuery(".question").click(function(){
			jQuery(".navQuestion").hide();
			jQuery(".question").css("background-color","");			
			var jqThis = jQuery(this);
			var sibbling = jqThis.siblings();
			var data = jQuery.metadata.get(this);
			var _currentQuestion = Meeting.myApi.getQuestion(data.id);
						
			sibbling.show().css("background-color","white");;
			jqThis.css("background-color","white");	
			
			if (_currentQuestion.bIsMine) {
				sibbling.find(".captureTrigger").hide();
				sibbling.find(".editTrigger").show();
			}		
			else {
				sibbling.find(".releaseTrigger").hide();
				sibbling.find(".editTrigger").hide();
				sibbling.find(".captureTrigger").show();
			}
			
			var _theQuestion = _currentQuestion.question[0];
			if (_theQuestion.bIsPublished == 1) {
				sibbling.find(".publishTrigger").hide();
				sibbling.find(".unpublishTrigger").show();			
			}
			else {
				sibbling.find(".publishTrigger").show();
				sibbling.find(".unpublishTrigger").hide();
			}
			if (_theQuestion.bIsAccepted == 1) {
				sibbling.find(".acceptTrigger").hide();
				sibbling.find(".rejectTrigger").show();			
			}
			else {
				sibbling.find(".acceptTrigger").show();
				sibbling.find(".rejectTrigger").hide();
			}
			
			Meeting.currentOpen = data.id;				
			
		});
	}
	,captureQuestion : function(id){
		Meeting.myApi.captureQuestion(id);		
	}
	,releaseQuestion : function(id) {
		Meeting.myApi.releaseQuestion(id);
	}
	,editQuestion : function (item) {
		Meeting.questionToEdit 	= Meeting.myApi.getQuestion(jQuery.metadata.get(item));
		var _answer = Meeting.questionToEdit.answerContent;
		Meeting.questionToEdit	= Meeting.questionToEdit.question[0];
		
		Meeting.questionContent = jQuery("#questionContent");
		Meeting.questionAlias 	= jQuery("#questionAlias");
		Meeting.questionDate	= jQuery("#questionDate");
		Meeting.answerContent 	= jQuery("#answerContent");
		Meeting.saveTrigger		= jQuery("#saveTrigger");
		
		Meeting.questionContent.val(Meeting.questionToEdit.content);
		Meeting.questionAlias.html(Meeting.questionToEdit.alias);
		Meeting.questionDate.html(Meeting.questionToEdit.dateCreated);
		//todo: get the answer that is already there and make it possible to edit.
		
		Meeting.answerContent.val(_answer);		
		Meeting.saveTrigger.attr("disabled", "");
		
		Meeting.saveTrigger.click(function() {
			var _theQuestion = new Object();
			_theQuestion.questionContent	= Meeting.questionContent.val();
			_theQuestion.answerContent 		= Meeting.answerContent.val();
			_theQuestion.questionID 		= Meeting.questionToEdit.id;			
			Meeting.myApi.saveQA(_theQuestion);
			setTimeout("Meeting.refreshQuestionsPannel(false)", 100);
		});
	}
	,publishQuestion : function(item,b){
		Meeting.myApi.publishQuestion(jQuery.metadata.get(item).id,b);
		if (b == 1) {
			jQuery(".publishTrigger").hide();
			jQuery(".unpublishTrigger").show();
		}
		else {
			jQuery(".publishTrigger").show();
			jQuery(".unpublishTrigger").hide();
		}
	}
	,acceptQuestion : function(item,b){
		Meeting.myApi.acceptQuestion(jQuery.metadata.get(item).id,b);
		if (b == 1) {
			jQuery(".acceptTrigger").hide();
			jQuery(".rejectTrigger").show();
		}
		else {
			jQuery(".acceptTrigger").show();
			jQuery(".rejectTrigger").hide();
		}
	}
	,slashN2BR : function(str) {				
		return str.replace('\n', '<br>');
	}
	,fixLTGT : function(str) {
		var tmp = str.replace(/&lt;/g,'<');
		return tmp.replace(/&gt;/g,">");
	}
}
;
function d (v) {
	
	if(typeof(console) !== 'undefined' && console != null) {
		console.log(v);
	}
	else {
		alert(v);
	} 
		
	
}