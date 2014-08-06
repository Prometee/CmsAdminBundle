jQuery(document).ready(function(){
    jQuery(document)
        .on('cms_admin.collection_field_row_add', '.collection-field-row', function(){initTinyMCE()})
        .on('shown.bs.tab', 'a[data-toggle="tab"]', function(){initTinyMCE()});
});