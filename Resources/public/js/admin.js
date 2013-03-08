jQuery(document).ready(function(){
	
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
            if ($c.children().length == 0) {
                    index = 0;
            }
        };

		index = $c.children().length;
        if( jQuery('#list-photos .thumbnail').size() ){
            index = jQuery('#list-photos .thumbnail').size() + 1;
        }

        jQuery('.add-collection-row', $c.parent()).click(function(e) {
            e.preventDefault();
            add_row();
        });

        jQuery('.delete-collection-row', $c.parent()).click(function(e) {
            e.preventDefault();
            del_row(jQuery(this));
        });
    });
});