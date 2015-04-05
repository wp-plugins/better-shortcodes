(function($) {
	
	/*
	 * Handle File Upload Form field
	 */
	if( $(".restore-file").val().length == 0 ){
		$
	}
// MIGHT USE FOR IE in an update
// $( '.restore-file' ).on( 'click', function updateFileName( event ){
//     var $input = $( this );
//     setTimeout( function delayResolution(){
//         $input.parent().text( $input.val().replace(/([^\\]*\\)*/,'') )
//     }, 0 )
// } );
	$(".restore-file").change(function(){
		if($(this).length){
			$(".step-two").css("height","auto").animate({opacity:1},600);
			labeltxt = $(this).val();
			lbl = document.getElementById('fileInputLbl');
			var txt_change = lbl.childNodes[0];
			txt_change.nodeValue = labeltxt.replace(/([^\\]*\\)*/,''); 
			$(".file-input").addClass("chosen");
			$(".button-primary").animate({opacity:1},600).removeAttr("disabled");
		}
		else {
			lbl = document.getElementById('fileInputLbl');
			var txt_change = lbl.childNodes[0];
			txt_change.nodeValue = "Browse to Select File";
			$(".file-input").addClass("chosen");
			$(".button-primary").animate({opacity:0},600).addAttr("disabled");
		}

	});

})(jQuery);

/**
* Validation
* Only allow json extenstions
*/

jQuery("#restore-form").validate({
	rules: {
		restore: {
			required: true,
			extension: "json"
		}
	},
	messages: {
		restore: {
			extension: "Please select a file with an extenstion of .json"
		}
	}
});