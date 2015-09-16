window.addEvent('domready', function(){ 
	
	// Validation script
    Joomla.submitbutton = function(task){
        if (task == 'email.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
    };
    
});