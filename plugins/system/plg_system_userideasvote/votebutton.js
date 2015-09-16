jQuery(document).ready(function() {

	jQuery(".js-ui-btn-vote").on("click", function(event){
		event.preventDefault();
				
		var id  = jQuery(this).data("id");
		var url = "index.php?option=com_userideas&task=item.vote&format=raw";
		
		var fields = {
			id: id
		};
		
		jQuery.ajax({
			type: "POST",
			url:  url,
			data: fields,
			dataType: "text json"
		}).done(function( response ) {
			
			if(response.success) {
				PrismUIHelper.displayMessageSuccess(response.title, response.text);
				jQuery("#js-ui-vote-counter-"+id).html(response.data.votes);
			} else {
				PrismUIHelper.displayMessageFailure(response.title, response.text);
			}
			
		});
		
	});
});