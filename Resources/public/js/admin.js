jQuery(document).ready(function(){
    
    if (jQuery("[rel=tooltip]").length) {
        jQuery("[rel=tooltip]").tooltip();
    }
	
	jQuery(document).on('click', '.btn-confirm', function(e) {
		if (!window.confirm(jQuery(this).data('title'))) {
			e.preventDefault();
		}
	});
	
	//Sortable Extension
    if (jQuery('.table-order').length > 0) {
        jQuery('.table-order tbody').sortable().disableSelection();
    }
});