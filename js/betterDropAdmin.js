(function($) {
	
	/*
	 * initialize the table of shortcodes for sorting
	 */
	$(".form-table").sortable({
		items: "tr:not(.ui-state-disabled)",
		placeholder: "ui-state-highlight",
		helper: fixHelper
	});
	/*
	 * Make Sortable Rows Work
	 */
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;// Return proper cell width
	};	
	/*
	 * Set Form Value(s) for the Admin Page
	 */
  var codeContainer = $('.form-table');
  var j = $('.form-table tr').size() + 1;
	var obj = $.parseJSON($('#btslb_shortcodes').val());
	var l = 0;
	if(obj!=null){
		l = obj.length;
		console.log('length: ' + l);
	}
	if(obj){		
		$.each(obj,function(idx,obj){
			var tmp_stop  = obj.value.lastIndexOf('_');//shortcodes[key].lastIndexOf('_');
			var checked   = parseInt(obj.value.substring(tmp_stop + 1, obj.value.length));//parseInt(shortcodes[key].substring(tmp_stop + 1,shortcodes[key].length));
			
			if(l==1){
				//addValues(shortcodes[key].text,decodeURI(shortcodes[key].value.substring(0,tmp_stop)),checked,1);
					console.log('idx: ' + idx);
					console.log('obj: ' + obj.text);
				addValues(obj.text,decodeURI(obj.value.substring(0,tmp_stop)),checked,1);
			}
			else if(l>1) {
				if(j==4){
					console.log('idx: ' + idx);
					console.log('obj: ' + obj);
					addValues(obj.text,decodeURI(obj.value.substring(0,tmp_stop)),checked,1);
					j++;
				}
				else {
					addShortCodeRow(codeContainer,j);
					addValues(obj.text,decodeURI(obj.value.substring(0,tmp_stop)),checked,j);
					j++;
				}
			}
						
		});	
	}
	/*
	 * Add Values to Form Fields
	 */
	function addValues(k,v,c,i){
		$(".btslb_n_"+i).val(k);
		$(".btslb_c_"+i).val(v);
		if(c){
			$(".btslb_d_"+i).prop("checked",true);
		}		
	}
	/*
	 * event listener for Save form action
	 */
	$("#btslb-options-form").submit(function(event){
		if($(this).hasClass("prep")){
			event.preventDefault();
		}
		else {
			$(this).addClass("prep");
			combineFields();
		}
	});
	/*
	 * Submit form function
	 */
	function submitForm(theForm){
		$(theForm).submit();
	}
	/*
	 * filter any invalid characters from form fields
	 */
	$(".btslb-filter").keyup(function() {
	    var input = $(this),
	    text = input.val().replace(/[^a-zA-Z0-9-_="\s]/g, "");
	    /*maybe replace spaces? (this works if uncommented)
	    if(($(input).hasClass('nospace'))&& (/_|\s/g.test(text))) {
	        text = text.replace(/_|\s/g, "_");//replace space with _
	        // possibly add logic to notify user of replacement
	    }
	    */
	    input.val(text);
	});

	/*
	 * create a JSON string from form shortcode elements
	 */	
 	function combineFields(){
	 	var savedNames = [];
	 	var savedCodes = [];
	 	var savedClose = [];
	 	var savedJSON  = [];
	 	var tmp_j;
	 	
		var input_n = $('input.btslb_n'), tmp_n;
		$.each(input_n, function(i, obj) {
		  tmp_n = $(obj).val();
		  if(tmp_n){
				savedNames.push(tmp_n);
			}
		});
		
		var input_c = $('input.btslb_c'), tmp_c;
		$.each(input_c, function(i, obj) {
		  tmp_c = $(obj).val();
		  if(tmp_c){
				savedCodes.push(encodeURI(tmp_c));//catch double quotes etc...
			}
		});
		
		var input_d = $('input.btslb_d'), tmp_d;
		$.each(input_d, function(i, obj) {
		  tmp_d = $(obj).is(':checked');
		  if(tmp_d){
				savedClose.push('1');
			}
			else {
				savedClose.push('0');
			}
		});
		
		for(var j = 0; j < savedNames.length; j++){
			tmp_j = '{"text":"'+savedNames[j]+'","value":"'+savedCodes[j]+'_'+savedClose[j]+'"}';
			savedJSON.push(tmp_j);
		}
		$('#btslb_shortcodes').val('['+savedJSON.toString()+']');
		
		/*
		 * Submit the form
		 */
		submitForm("#btslb-options-form");	 	
	}
	/*
	 * Handle New Form Fields
	 */
    
    $('#addCode').on('click', function() {
			addShortCodeRow(codeContainer,j);
			j++;
			return false;
		});        		
    		
    $('.form-table').on('click', '.remCode', function() { 
        if( j > 3 ) {
        	$(this).parent().animate({opacity: 0},500,function(){
	          $(this).parents('tr').remove();
	          j--;
         });
        }
        return false;
    });

	/*
	 * add Short Code Row to Admin Page
	 */
	function addShortCodeRow(codeContainer,i){
		$('<tr class="even" valign="top">' +
				'<th scope="row"><label for="tmp_btslb_freindly"></label></th>' +				
				'<td>' +
					'<input type="text" id="tmp_btslb_friendly" name="tmp_btslb_friendly" class="medium-text friendly btslb_n btslb_n_' + i +' btslb-filter" placeholder="Friendly Name"/>&nbsp;' +
					'<input type="text" id="tmp_btslb_shortcodes" name="tmp_btslb_shortcodes" class="regular-text shortcode btslb_c btslb_c_' + i +' btslb-filter nospace" placeholder="Shortcode"/>&nbsp;' +
					'<a href="javascript:void(0)" class="button-secondary remCode">Remove</a>' +
					'<label class="btslb_checkbox"><input id="tmp_btslb_do_not_close' + i +'" name="tmp_btslb_do_not_close" type="checkbox" value="1" class="btslb_d btslb_d_' + i +'">Do Not Auto Close</label>' +						
				'</td>' +
			'</tr>'
		).appendTo(codeContainer);
		$(".form-table").sortable("refresh");
	}
	
	/*
	 * Help Section
	 */
	$('#ex').on('click',function(){
			$(this).toggleClass('open');
			if($(this).hasClass('open')){
				$(this).html('Close Help');
				var curHeight = $('.examples ul').first().height();
				var autHeight = $('.examples ul').first().css('height','auto').height();
				$('.examples ul').first().height(curHeight).animate({height: autHeight},500);
			}
			else {
				$(this).html('Help');
				$('.examples ul').first().animate({height: 0},500);
			}
		});

})(jQuery);