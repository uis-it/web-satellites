var myForum = {	
			'bindStatusTriggers' : function(){	
			
			jQuery(".inputText").click(function() {
				myForum.createTextBoxForm(this,"forum_update_field");
			});
			jQuery(".inputTextArea").click(function() {
				myForum.createTextAreaForm(this,"forum_update_field");
			});
			
						
			jQuery(".changeStatusTrigger").click(function(){
				var theThis = jQuery(this); 
				var id = theThis.attr('id');
				id = id.split("_");
				var newStatus = id[0];
				var bApproved = 9;
				id = id[1];
				if (newStatus == 'accept'){
					bApproved = 1;
					jQuery('#reject_'+id).attr('checked','');
				}
				else {
					bApproved = 0;
					jQuery('#accept_'+id).attr('checked','');
				}

				jQuery.ajax({
					type:"GET"
					,cache:false
					,data:{
						bApproved: bApproved
						,id: id							
					}
					,url:"forum_change_status.php"
					,async: false						
				})
				
			});
		}
		,'createTextBoxForm': function (theElement,saveMethodName){
			
			var _theValue = jQuery(theElement).html();
			var _id = jQuery(theElement).attr("id");
			var _txt = jQuery(document.createElement("input"));
			var _field = jQuery(theElement).attr("field");
			_txt
				.attr("type","text")
				.attr("value",_theValue)
				.attr("size","50")
				.attr("id",_id);				
									
			jQuery(theElement).html(_txt);	
			jQuery(theElement).unbind( "click" );
			jQuery(_txt).trigger("focus");
			jQuery(_txt).keypress(checkEnter);
			jQuery(_txt).blur(function(){         			
         			var _theNewValue = jQuery(_txt).val();
         			var _theId = jQuery(_txt).attr("id");
         			jQuery(theElement).html(_theNewValue);
         			jQuery.ajax({
						type: 'POST',
						url: saveMethodName+'.php',	
						data: {
							id: _theId
							,newValue: _theNewValue
							,field: _field
						},
						success: function(html){								
							if (html == "NAN"){
								jQuery(theElement).html("1");
							}
						}
					});	         			
         			jQuery(theElement).click(
         				function (){
         					myForum.createTextBoxForm(theElement,saveMethodName);
         			});
   			});									
		}
		,'createTextAreaForm': function (theElement,saveMethodName){
			var _theValue = jQuery(theElement).html();
			var _id = jQuery(theElement).attr("id");
			var _txt = jQuery(document.createElement("textarea"));
			var _OKButton = jQuery(document.createElement("input"));
			_OKButton
					.attr("type","button")
					.attr("value","Done");
			
			_txt.append(_OKButton);

			var _field = jQuery(theElement).attr("field");
			_txt.val(_theValue)
					.attr("id",_id)
					.attr("cols",50)
					.attr("rows",10);
			
								
			jQuery(theElement).html(_txt);
			jQuery(theElement).append(_OKButton);
				
			jQuery(theElement).unbind( "click" );
			jQuery("textarea").wysiwyg();
			jQuery(_txt).trigger("focus");
			
			_OKButton.click(function(){         			
         			var _theNewValue = jQuery(_txt).val();
         			var _theId = jQuery(_txt).attr("id");
         			jQuery(theElement).html(_theNewValue);
         			jQuery.ajax({
						type: 'POST',
						url: saveMethodName+'.php',	
						data: {
							id: _theId
							,newValue: _theNewValue
							,field: _field
						},
						success: function(html){								
							if (html == "NAN"){
								jQuery(theElement).html("1");
							}
							jQuery(theElement).html(_theNewValue);		
							jQuery(theElement).click(
		         				function (){
		         					myForum.createTextAreaForm(theElement,saveMethodName);
		         			});						
						}
					});	         			
         			jQuery(theElement).click(
         				function (){
         					myForum.createTextAreaForm(theElement,saveMethodName);
         			});
   			});									
		}
		
}