window.addEvent('domready', function(){ 
	
	// Validation script
    Joomla.submitbutton = function(task){
        if (task == 'status.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
    };
    
});