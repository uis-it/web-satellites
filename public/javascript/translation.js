

var myForm = {	
		numberOfRows : 0
		,typeToSave : 0	
		,bindEditTriggers : function(){
			jQuery(".saveTrigger").click(function(){
				//myForm.saveToFile();
				alert("Save");
			});
		}
		,saveToFile : function(){
			var jsonPost 	= Array();
			var item 		= {};
			var typeToSave 	= "";
			switch (myForm.typeToSave) {
				case 1: {
					typeToSave 	= "position";
					break;
				}
				case 2: {
					typeToSave 	= "department";
					break;
				}
				case 3: {
					typeToSave 	= "institute";
					break;
				}
				case 4: {
					typeToSave 	= "competence";
					break;
				}
			}	
			if (typeToSave == "competence") {
				var sRet 		= {};
				var tmpArray 	= new Array();
				
				list = jQuery(".NO").each(function(){
					theThis 	= jQuery(this);
					var id 		= theThis.attr("id");
					var tmp 	= id.split("::");					
					var item	= {};
					item.id1	= tmp[0];
					item.id2	= tmp[1];
					item.value 	= theThis.val();
					tmpArray.push(item);
				});
				sRet.no = tmpArray;
				var tmpArray 	= new Array();
				list = jQuery(".EN").each(function(){
					theThis 	= jQuery(this);
					var id 		= theThis.attr("id");
					var tmp 	= id.split("::");					
					var item	= {};
					item.id1	= tmp[0];
					item.id2	= tmp[1];
					item.value 	= theThis.val();
					tmpArray.push(item);
				});
				sRet.en = tmpArray;
				
				jsonPost = jQuery.toJSON(sRet);
			}
			else 
			{
				for (var i=0;i <= myForm.numberOfRows;i++) {
					var item	= {};
					item.key 	= jQuery("#"+typeToSave+"_"+i).val();
					item.no		= jQuery("#"+typeToSave+"_no_"+i).val();
					item.en		= jQuery("#"+typeToSave+"_en_"+i).val();
					jsonPost.push(item);				
				}
				jsonPost = jQuery.toJSON(jsonPost);
			}		
			jQuery.ajax({			
				type:"POST"
				,cache:false
				,data:{
					jsonPost : jsonPost								
				}
				,url:"languagesFilesEditorAjaxController.php?action=save&file="+typeToSave
				,async: false
				,success: function(d) {			
					alert("DONE!");									
				}
			});
		}
	}	
	jQuery(function(){				
		$("#tabs").tabs();
				
	});