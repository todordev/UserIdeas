jQuery(document).ready(function() {

	var elementClasses = ".js-ui-btn-vote";
	if (userIdeas.counter_button == 1) {
		jQuery('.js-ui-item-counter').addClass('cursor-pointer');
		elementClasses = elementClasses + ', .js-ui-item-counter';
	}

	jQuery(elementClasses).on("click", function(event){
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

                var $moduleVotes    = jQuery("#js-ui-mod-vote-counter-"+id);
                var $componentVotes = jQuery("#js-ui-vote-counter-"+id);

                if ($moduleVotes) {
                    $moduleVotes.text(response.data.votes);
                }

                if ($componentVotes) {
                    $componentVotes.text(response.data.votes);
                }

			} else {
				PrismUIHelper.displayMessageFailure(response.title, response.text);
			}
		});
	});
});