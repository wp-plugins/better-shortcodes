(function() {
	tinymce.create('tinymce.plugins.shortcodedrop', {
		init          : function(ed, url) {},
		createControl : function(n, cm) {
			if(n=='shortcodedrop'){
				var mlb = cm.createListBox('shortcodedrop', {
										title    : listTitle,
										onselect : function(v) {
																if(v!=''){
																	var open = '[';
																	var close = ']';
																	var openclose = '[/';
																	if(tinyMCE.activeEditor.selection.getContent() == ''){
																		tinyMCE.activeEditor.selection.setContent( open + v + close + openclose + v + close );
																	}
																	else {
																		tinyMCE.activeEditor.selection.setContent(open + v + close + tinyMCE.activeEditor.selection.getContent() + openclose + v + close);
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