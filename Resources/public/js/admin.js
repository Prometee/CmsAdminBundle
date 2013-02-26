jQuery(document).ready(function(){
	
	// Twitter Bootstrap | Alerts
    // ===============================
    jQuery(".alert").alert();

    // Twitter Bootstrap | Dropdown
    // ===============================
    jQuery('.dropdown-toggle').dropdown();

    // Twitter Bootstrap | Popover
    // ===============================
    jQuery('a[rel=popover]').popover();

    // Aside Navigation
    // .toplevelnav
    // Dropdown menu
    // ============
    jQuery('.toplevelnav li.withsubsections > a').on('click', function(event) {
        event.preventDefault();
        var $liParent = jQuery(this).parent();
        if ($liParent.hasClass('open')) {
            $liParent.find('.subsections').slideUp(400, function () {
                $liParent.toggleClass('open');
            });
        } else {
            $liParent.find('.subsections').slideDown(400, function () {
                $liParent.toggleClass('open');
            });
        }
    });

    // Selected all rows in table
    // ============
    jQuery('table').on('change', 'thead .groupCheckbox :checkbox', function() {
        var $table = jQuery(this).parentsUntil('table').parent();
        var selectAll = jQuery(this).is(':checked');

        $table.find('tbody .groupCheckbox :checkbox').each(function() {
            jQuery(this).prop('checked', selectAll);
            jQuery(this).trigger('change');
        });
    });
	
	// Twitter Bootstrap
    // add on logic
    // ============
    jQuery('.add-on :checkbox').on('click', function() {
        if (jquery(this).attr('checked')) {
          jQuery(this).parents('.add-on').addClass('active');
        } else {
          jQuery(this).parents('.add-on').removeClass('active');
        }
    });

    // Add class 'selected' on TR when the user check the checkbox
    // ============
    jQuery('table').on('change', 'tbody .groupCheckbox :checkbox', function() {
        if ( jQuery(this).is(':checked') ) {
            jQuery(this).parentsUntil('tr').parent().addClass('selected');
        } else {
            jQuery(this).parentsUntil('tr').parent().removeClass('selected');
        }
    });
	
	

    // Way to load inline an partial of form from one entity
    // Example: To insert in Agency form several Address form
    // ============
    jQuery('.ajaxLinkAddItemInline').on('click', function() {
        var id = jQuery(this).attr('id');
        var idContainer = '#' + id + '-container';
        var num = jQuery(idContainer).data('countItems');
        num = (num > 0) ? num : 0;

        var text = jQuery.ajax({
            type: 'GET',
            url: Routing.generate(
                jQuery(this).data('route'),
                {num: num, entity: jQuery(this).data('targetEntity')}),
            async: false
        }).responseText;

        jQuery(idContainer).append(text);
        jQuery(idContainer).data('countItems', num + 1);
        jQuery('body').trigger('bsky_jqueryautocomplete_init');
    });

    // See above
    // ============
    jQuery('.ajaxLinkDeleteItem').on('click', function(e){
        e.preventDefault();
        jQuery(this).parents('.clearfix:first').remove();
    });

    // Twitter Popover Action
    // ============
    jQuery('.bui-confirm-popover, .bui-confirm-popover-left, .bui-confirm-popover-right, .bui-confirm-popover-above, .bui-confirm-popover-below').on('click', function(event) {
        event.preventDefault();
        // Variable initialization
        var title, $parent, parentSize, $popover, $bui;

        title = jQuery(this).data('popoverTitle');
        parentSize = {width: 0, height: 0};

        // Set link as disabled
        jQuery(this).addClass('disabled');

        // If the popover has already created
        if (jQuery(this).hasClass('wrapped-for-popover')) {
            jQuery(this).parents('.bui.btt-action-popover-wrapper').find('.popover').show();
            return;
        }

        // Retrieve the position of popover
        var positionPopoverClass = 'left';
        if (jQuery(this).hasClass('bui-confirm-popover-right')) {
            positionPopoverClass = "right";
        } else if (jQuery(this).hasClass('bui-confirm-popover-below')) {
            positionPopoverClass = "bottom";
        } else if (jQuery(this).hasClass('bui-confirm-popover-above')) {
            positionPopoverClass = "top";
        }

        // Wrap the link by the popover
        jQuery(this).wrap('<div class="bui btt-action-popover-wrapper"></div>');

        $parent = jQuery(this).parent();

        $parent.prepend('<div class="popover '
            + positionPopoverClass
            + '"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title">'
            + title
            + '</h3><div class="popover-content"><a class="btn btn-mini btn-danger" href="'
            + jQuery(this).attr('href')
            + '">Valider</a>&nbsp;<a class="btn btn-mini cancel">Annuler</a></div></div></div>');

        // Bind the cancel action
        $parent.find('.popover-inner .btn.cancel').on('click', function() {
            jQuery(this).parents('.popover').hide();
            jQuery(this).parents('.btt-action-popover-wrapper').find('.wrapped-for-popover').removeClass('disabled');
        });

        $popover = $parent.find('.popover');
        $bui = jQuery(this);

        // Custom the style of elements
        parentSize.width = parseInt($bui.outerWidth()) + parseInt($bui.css('margin-left').replace("px", "")) + parseInt($bui.css('margin-right').replace("px", "")) +3;
        parentSize.height = parseInt($bui.outerHeight()) + parseInt($bui.css('margin-top').replace("px", "")) + parseInt($bui.css('margin-bottom').replace("px", ""));
        $parent.css('width', parentSize.width + "px");
        $parent.css('height', parentSize.height + "px");

        $popover.css('height', $popover.height());
        $popover.css('width', $popover.width());

        if ("right" == positionPopoverClass) {
            $popover.css('top', "-" + Math.round(($popover.outerHeight() / 2) - $bui.height() / 2) + "px");
            $popover.css('right', "-" + ($popover.outerWidth() + 2) + "px");
            $popover.css('left', "auto");
            $popover.css('bottom', "auto");
        } else if ("bottom" == positionPopoverClass) {
            $popover.css('bottom', "-" + ($popover.outerHeight() + 2) + "px");
            $popover.css('right', "-" + Math.round(($popover.outerWidth() / 2) - ($bui.outerWidth() / 2)) + "px");
            $popover.css('top', "auto");
            $popover.css('left', "auto");
        } else if ("top" == positionPopoverClass) {
            $popover.css('left', "auto");
            $popover.css('bottom', "auto");
            $popover.css('top', "-" + ($popover.outerHeight() + 2) + "px");
            $popover.css('right', "-" + Math.round(($popover.outerWidth() / 2) - ($bui.outerWidth() / 2)) + "px");
        } else {
            $popover.css('top', "-" + Math.round(($popover.outerHeight() / 2) - $bui.height() / 2) + "px");
            $popover.css('right', ($bui.outerWidth() + 2) + "px");
            $popover.css('left', "auto");
            $popover.css('bottom', "auto");
        }

        // Mark the link as wrapped by the popover
        jQuery(this).addClass('wrapped-for-popover');

        // Show the popover
        $popover.show();
    });

    //
    // On AJAX Waiting
    // ============
    var ajaxOnLoading;
    var elAjaxOnLoading = '#ajax-on-loading';
    jQuery(elAjaxOnLoading).ajaxStart(function() {
        clearTimeout(ajaxOnLoading);
        ajaxOnLoadignMessage('.loading');
        ajaxOnLoadingWidth();
        jQuery(elAjaxOnLoading).slideDown();
    });
    jQuery(elAjaxOnLoading).ajaxSuccess(function() {
        ajaxOnLoadingWidth();
        ajaxOnLoadignMessage('.success');
    });
    jQuery(elAjaxOnLoading).ajaxError(function() {
        ajaxOnLoadignMessage('.error');
        ajaxOnLoadingWidth();
    });
    jQuery(elAjaxOnLoading).ajaxStop(function() {
        clearTimeout(ajaxOnLoading);
        ajaxOnLoading = setTimeout(function() {
            jQuery(elAjaxOnLoading).slideUp();
        }, 2500);
    });

    var ajaxOnLoadingWidth = function () {
        var left = (jQuery(window).width() - jQuery(elAjaxOnLoading).innerWidth()) / 2;
        jQuery(elAjaxOnLoading).css('left', Math.round(left) + 'px');
    };
    var ajaxOnLoadignMessage = function (type) {
        var hide = '.success, .error';
        if (type == '.success') {
            hide = '.error, .loading';
        } else if (type == '.error') {
            hide = '.success, .loading';
        }
        jQuery(hide, elAjaxOnLoading).css('display', 'none');
        jQuery(type, elAjaxOnLoading).css('display', 'inline');
    };

    //
    // On portion area
    // ============
    jQuery('.portion .portion-header').on('click', function() {
       var parent = jQuery(this).parent();
       parent.find('.portion-container').slideToggle(1000, function() {
           if (jQuery(this).is(':hidden')) {
               parent.removeClass('open');
           } else {
               parent.addClass('open');
           }
       });
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