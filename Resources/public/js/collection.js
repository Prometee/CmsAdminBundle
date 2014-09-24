var container_selector = '.collection-fields',
    add_btn_selector = '.add-collection-row',
    delete_btn_selector = '.delete-collection-row',
    data_prototype_selector = '[data-prototype]',

    collection_field_row_add = function(e) {
        e.preventDefault();
        var $el = jQuery(this),
            parents = $el.parentsUntil(data_prototype_selector),
            $p = (parents.length > 0 ? parents.last() : $el).parent(),
            $c = $p.find(container_selector).first(),
            data_prototype = $p.data('prototype'),
            $row = jQuery(jQuery('<div class="new" />')
                .append(data_prototype.replace(/__name__/g, $c.data('index')))
                .text());

        $c.append($row);

        $c.trigger('cms_admin.collection_field_row.add', [$row, $c, $p]);


        jQuery(container_selector, $row).each(process_collection_row);

        $c.data('index', $c.data('index')+1);
    },

    collection_field_row_delete = function(e) {
        e.preventDefault();
        var $el = jQuery(this),
            parents = $el.parentsUntil(data_prototype_selector),
            $p = (parents.length > 0 ? parents.last() : $el).parent(),
            $c = $p.find(container_selector).first(),
            $row = $el.parentsUntil(container_selector).last();
        $row
            .trigger('cms_admin.collection_field_row.delete', [$row, $c, $p])
            .remove();
    },

    process_collection_row = function() {
        var $c = jQuery(this);
        $c.data('index', ($c.data('index') > 0) ? $c.data('index') : $c.children().length);
    };

jQuery(document).ready(function(){
    // Collection fields
    // rendered with jQuery
    // ============
    jQuery(container_selector).each(process_collection_row);
    jQuery(document).on('click', delete_btn_selector, collection_field_row_delete);
    jQuery(document).on('click', add_btn_selector, collection_field_row_add);
});
