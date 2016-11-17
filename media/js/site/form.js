jQuery(document).ready(function() {
	"use strict";

	// Style file input
	jQuery('#jform_attachment').fileinput({
        theme: 'fa',
        showPreview: false,
        showUpload: false,
        browseClass: "btn btn-success",
        browseLabel: Joomla.JText._('COM_USERIDEAS_PICK_FILE'),
        removeClass: "btn btn-danger",
        removeLabel: Joomla.JText._('COM_USERIDEAS_REMOVE'),
        layoutTemplates: {
            main1:
            "<div class=\'input-group {class}\'>\n" +
            "   <div class=\'input-group-btn\'>\n" +
            "       {browse}\n" +
            "       {remove}\n" +
            "   </div>\n" +
            "   {caption}\n" +
            "</div>"
        }
    });

    // Set the event to button Remove.
    var btnRemoveAttachment = jQuery('.js-ui-btn-attachment-remove');
    if (btnRemoveAttachment.length) {
        btnRemoveAttachment.on('click', function (event) {
            event.preventDefault();

            var redirectUrl = jQuery(this).data('href');

            swal({
                title: Joomla.JText._('COM_USERIDEAS_ARE_YOU_SURE'),
                text: Joomla.JText._('COM_USERIDEAS_CANNOT_RECOVER_FILE'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: Joomla.JText._('COM_USERIDEAS_YES_DELETE_IT'),
                cancelButtonText: Joomla.JText._('COM_USERIDEAS_CANCEL'),
                closeOnConfirm: false
            },
            function () {
                window.location = redirectUrl;
            });
        });
    }

});