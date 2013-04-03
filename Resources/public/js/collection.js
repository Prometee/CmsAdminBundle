var collection_field_selector = '.collection-fields';

var process_collection_row = function() {
    var index = 0,
        $c = jQuery(this);

    $c.data('index', ($c.data('index') > 0) ? $c.data('index') : $c.children().length);

    jQuery('>.add-collection-row:first', $c.parent())
        .data('parent', this)
        .on('click', function(e) {
            e.preventDefault();
            collection_field_row_add(jQuery(this));
            $c.data('index', $c.data('index')+1);
        });

    jQuery($c.parent()).on('click', '>.delete-collection-row', function(e) {
        e.preventDefault();
        collection_field_row_delete(jQuery(this));
    });
};

/**
 * Add a new row to the collection
 *
 * @context null
 */
var collection_field_row_add = function($el) {
    var $c = jQuery($el.data('parent')),
        data_prototype = $c.parent().attr('data-prototype'),
        del_btn = jQuery('.delete-collection-row:first', $c.parent());
    if (del_btn) {
        del_btn = del_btn.clone().removeClass('hide');
    }
    var html = jQuery('<div class="collection-field-row new" />')
            .append(data_prototype.replace(/__name__/g, $c.data('index'))
            .append(del_btn);

    $c.append(html);
    del_btn.click(function(e) {
        e.preventDefault();
        collection_field_row_delete($el);
    });
    jQuery(collection_field_selector, html).each(process_collection_row);
    return html;
};

/**
 * del row to the collection
 *
 * @context DomNode
 * @param e Event fired
 */
var collection_field_row_delete = function($el) {
    $el.parent().remove();
};

jQuery(document).ready(function(){
    // Collection fields
    // rendered with jQuery
    // ============
    jQuery(collection_field_selector).each(process_collection_row);
});
