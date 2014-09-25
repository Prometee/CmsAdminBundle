jQuery(document).ready(function(){
    jQuery(document)
        .on('cms_admin.collection_field_row.add', function(e, $row, $c, $p) {
            if ($row.hasClass('collection-field-row modal')) {
                var html = jQuery(jQuery('<div class="new" />')
                    .append($p.data('prototype-link').replace(/__name__/g, $c.data('index')))
                    .text());
                $p.find('.collection_modal_item_links').first().append(html);

                $row.modal('show');
            }
        })
        .on('cms_admin.collection_field_row.delete', function(e, $row, $c, $p) {
            if ($row.hasClass('collection-field-row modal')) {
                $row.modal('hide');
                $p.find('a[href=#'+$row[0].id+'], [data-href=#'+$row[0].id+']').remove();
            }
        });
});
