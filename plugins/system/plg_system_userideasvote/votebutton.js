jQuery(document).ready(function() {

	jQuery(".uf-btn-vote").on("click", function(event){
		event.preventDefault();
				
		var id  = jQuery(this).data("id");
		var url = "index.php?option=com_userideas&task=item.vote&format=raw";
		
		var fields = {
			id: id
		};
		
		jQuery.ajax({
			type: "POST",
			url:  url,
			data: fields
		}).done(function( response ) {
			
			var response = jQuery.parseJSON(response);
			
			if(response.success) {
				UserIdeasHelper.displayMessageSuccess(response.title, response.text);
				jQuery("#uf-vote-counter-"+id).html(response.data.votes);
				
			} else {
				UserIdeasHelper.displayMessageFailure(response.title, response.text);
			}
			
		});
		
		
	});
});