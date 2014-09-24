jQuery(document).ready(function(){
    jQuery(document)
        .on('cms_admin.collection_field_row_add', function(){initTinyMCE()})
        .on('shown.bs.tab, show.bs.modal', function(){initTinyMCE()});
});