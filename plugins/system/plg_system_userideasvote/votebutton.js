jQuery(document).ready(function() {

	jQuery(".js-ui-btn-vote").on("click", function(event){
		event.preventDefault();

        var id = jQuery(this).data("id");
		var fields = {
			id: id
		};

        jQuery.extend(fields, userIdeas.token);

		jQuery.ajax({
			type: "POST",
			url:  userIdeas.url,
			data: fields,
			dataType: "text json"
		}).done(function( response ) {
			
			if(response.success) {
				PrismUIHelper.displayMessageSuccess(response.title, response.text);

                var $moduleVotes = jQuery("#js-ui-mod-vote-counter-"+id);
                var $componentVotes = jQuery("#js-ui-vote-counter-"+id);

				console.log($moduleVotes);
				console.log($componentVotes);
                if ($moduleVotes) {
                    $moduleVotes.html(response.data.votes);
                }

                if ($componentVotes) {
                    $componentVotes.html(response.data.votes);
                }

			} else {
				PrismUIHelper.displayMessageFailure(response.title, response.text);
			}
			
		});
		
	});
});