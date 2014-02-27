(function() {
	tinymce.create('tinymce.plugins.shortcodedrop', {
		init          : function(ed, url) {},
		createControl : function(n, cm) {
			if(n=='shortcodedrop'){
				var mlb = cm.createListBox('shortcodedrop', {
										title    : listTitle,
										onselect : function(v) {
																if(v!=''){
																	var clean_code   = decodeURI(v.substring(0,v.length-2));
																	var close_code   = clean_code;
																	var space = '';
																	if((space = clean_code.indexOf(' '))>0){
																		close_code = clean_code.substring(0,space); //get only the name of the shortcode to close it properly
																	}
																	
																	var close_option = parseInt(v.substring(v.length - 1, v.length));
																	
																	var open = '[';
																	var close = ']';
																	var openclose = '[/';
																	
																	if(tinyMCE.activeEditor.selection.getContent() == ''){ //no content to wrap
																		if(!close_option){ // auto close tag
																			tinyMCE.activeEditor.selection.setContent( open + clean_code + close + openclose + close_code + close );
																		}
																		else {
																			tinyMCE.activeEditor.selection.setContent( open + clean_code + close );
																		}
																	}
																	else {// possibly wrap content
																		if(!close_option){ //auto close tag - wrap content
																			tinyMCE.activeEditor.selection.setContent(open + clean_code + close + tinyMCE.activeEditor.selection.getContent() + openclose + close_code + close);
																		}
																		else { // do not close tag - place content at end (just in case)
																			tinyMCE.activeEditor.selection.setContent(open + clean_code + close + tinyMCE.activeEditor.selection.getContent());
																		}																		
																	}
																}														
									}
		}
		);
			for (var i = 0; i<btslb_shortcodes.length; i++) {
				var obj = btslb_shortcodes[i];
					for (var key in obj) {
							mlb.add(key,obj[key]);							
					}				
			}	
			return mlb;	
		}	 
		return null;
		}
	});
	if(btslb_shortcodes.length){
		tinymce.PluginManager.add('shortcodedrop', tinymce.plugins.shortcodedrop);
	}
	/*
	 * ensure editor focus is set
	 */
	jQuery(".wp-editor-container").on("click","table[id*='shortcodedrop']",function(){
		jQuery(this).closest("tr.mceFirst").next("tr").children("td").children("iframe").focus();
	});
	/*
	 * simple fields plugin (or multiple tinymce editors)
	 */
	jQuery(".wp-editor-container").on("click","table[class*='shortcodedrop']",function(){
		jQuery(this).closest("tr.mceFirst").next("tr").children("td").children("iframe").focus();
	});	

})();