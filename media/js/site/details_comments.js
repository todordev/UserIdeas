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
});