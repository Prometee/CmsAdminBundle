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
	
	// Collection fields
    // rendered with jQuery
    // ============
    jQuery('.collection-fields').each(function() {
        var index = 0,
            $c = jQuery(this),
            data_prototype = $c.parent().attr('data-prototype');
        /**
            * Add a new row to the collection
            *
            * @context null
            */
        var add_row = function() {
            var del_btn = jQuery('.delete-collection-row:first', $c.parent());
            if (del_btn) {
                    del_btn = del_btn.clone().css('display', 'inline-block');
            }
            var html = jQuery('<div class="collection-field-row new" />')
                    .append(data_prototype.replace(/__name__/g, index))
                    .append(del_btn);

            $c.append(html);
            del_btn.click(function(e) {
                    e.preventDefault();
                    del_row(jQuery(this));
            });
            index++;
            return html;
        };

        /**
            * del row to the collection
            *
            * @context DomNode
            * @param e Event fired
            */
        var del_row = function(el) {
            el.parent().remove();
        };

		index = ($c.data('start-index') > 0) ? $c.data('start-index') : $c.children().length;

        jQuery($c.parent()).on('click', '.add-collection-row', function(e) {
            e.preventDefault();
            add_row();
        });

        jQuery($c.parent()).on('click', '.delete-collection-row', function(e) {
            e.preventDefault();
            del_row(jQuery(this));
        });
    });
	
	//Sortable Extension
	jQuery('.table-order tbody').sortable().disableSelection();
});