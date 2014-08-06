jQuery(document).ready(function(){
    
    jQuery('.checkAll').on('change', function () {
        jQuery(this).parent().parent().parent().parent().find('.checkAllItem').prop('checked', this.checked);
    });
	
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