jQuery(document).ready(function(){
    
    if (jQuery("[rel=tooltip]").length) {
        jQuery("[rel=tooltip]").tooltip();
    }
	
	jQuery(document).on('click', '.btn-confirm', function(e) {
		if (!window.confirm(jQuery(this).data('title'))) {
			e.preventDefault();
		}
	});
	
	jQuery('.form-horizontal .entity-collections .collection-field-row:before').css({
		'content': jQuery('input:first',this).val()
	});
	
	//Sortable Extension
	jQuery('.table-order tbody').sortable().disableSelection();
});