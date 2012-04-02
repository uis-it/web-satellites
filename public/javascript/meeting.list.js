var MeetingList =  {
	init: function() {
		this.myApi 		= jQuery.Zend.jsonrpc({url: '/json.php'});
		this.theList	= [];
		this.div_list 	= jQuery("#theList");
		this.theMenuBox	= "";
		//just for test
		this.trigger	= jQuery("#triggerReload");
		this.bindFunctionalities();
		this.getListAndDisplay();
		this.createMenuBox();
		jQuery.metadata.setType("attr", "data");
	}
	,createDopdown : function() {
		var _div = jQuery("<div></div>")
					.attr("id","dopdownBox")
		;
	}
	,bindFunctionalities : function() {		
		this.trigger.click(function(d){ MeetingList.getListAndDisplay(); });		
	}
	,getListAndDisplay : function() {		
		this.div_list.html("");
		this.getList();
		this.displayList();
	}	
	,getList : function(){
		this.theList = this.myApi.meeting_getMeetings();
	}

	,displayList : function() {
		for (var i = 0;i < this.theList.length; i++){
			var item 	= this.theList[i];
			var _div	= jQuery("<div></div>")
							.attr("class","meeting-item")	
							.attr("data","{id:"+item.id+"}")						
						;
			var _title = jQuery("<span></span>")
							.attr("class","meeting-tile")
							.html(item.title)
						;
			
			var _description = "  "; 
			
			/*
			if (item.description_small.length) {
				_description = item.description_small;
			}
			
			
			var _description = jQuery("<span></span>")
								.attr("class","meeting-description")
								.html(_description.substr(0,10)+" ...")						
							;
			var _qa 	= jQuery("<span></span>")
							.attr("class","meeting-qa")
							.html("14/5")
						;
			*/
			_div.append(_title);	
			//_div.append(_description);	
			//_div.append(_qa);
			this.div_list.append(_div);
			this.div_list.append();			
		}
		jQuery(".meeting-item").hover(
				function(){
					jQuery(this).addClass("meeting-item-selected");
				}
				,function(){
					jQuery(this).removeClass("meeting-item-selected");					
					MeetingList.theMenuBox.hide();
				}
		);				
		jQuery(".meeting-item").click(function(d){			
			MeetingList.showMenu(this);
		});
	}
	,createMenuBox : function () {
		var _div 		= jQuery("<div></div>")
							.attr("id","theMenu")							
						;
		var _spanEdit 	= jQuery("<div></div>")
							.attr("id","menuEditDiv")
							.append('<a id="menuEditLink" href="">Rediger artikkel</a>')
						;
		var _spanQA 	= jQuery("<div></div>")
							.attr("id","menuPanelDiv")
							.append('<a id="menuPanelLink" href="">Admin Q&A</a>')
						;
		
		_div	.append(_spanEdit)
				.append(_spanQA)
				.hide()
		;
		
		jQuery(document.body).append(_div);
		MeetingList.theMenuBox = _div;
	}
	,showMenu : function(div){
		var data 	= jQuery.metadata.get(div);
		var jqDiv 	= jQuery(div);
		MeetingList.theMenuBox
				.css("left",jqDiv.offset().left + jqDiv.width() - 100)
				.css("top",jqDiv.offset().top + 19)
		;		
		jQuery("#menuEditLink").attr("href","/meeting/edit/id/"+data.id);
		jQuery("#menuPanelLink").attr("href","/meeting/adminqa/id/"+data.id);
		jqDiv.append(MeetingList.theMenuBox);
		MeetingList.theMenuBox.show();
	}
}

	