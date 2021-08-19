$(function() {
    const DEBUG = true;

    $('.list-group-item').on('click', function() {
      $('.fas', this)
        .toggleClass('fa-angle-right')
        .toggleClass('fa-angle-down');
    });
    
    $('.sortable').nestedSortable({
        listType: 'ul',
        handle: 'a',
        items: 'li',
        toleranceElement: '> a',
        excludeRoot: true,
        relocate: () => {
            var hierarchy = $('.sortable').nestedSortable('toArray', {startDepthCount: 0});
            $.post('sort', { menuId: MENU_ID, items: hierarchy }, function (data) {
                if (DEBUG === true) {
                    console.table(data);
                }
            });
        }
    });

    $('.add-item').click( function(e) {
        e.preventDefault();
        window.location.href = '/menu/menu-item/create?menuId=' + MENU_ID + '&parentItemId=' + $(this).attr('data-id');
    });

    $('.list-group-item').click( function(e) {
        $.get('/menu/menu-item/view?id=' + $(this).attr('data-id'), function(data) {
            $('#menuitem-id').val(data.id);
            $('#menuitem-title').val(data.title).removeAttr('disabled');
            $('#menuitem-linkto').val(data.linkTo).removeAttr('disabled').trigger('change');
            $('#menuitem-linktarget').val(data.linkTarget).removeAttr('disabled').trigger('change');
            $('#menuitem-template').val(data.template).trigger('change');

            if (data.templateDisabled == false) {
                $('#menuitem-template').removeAttr('disabled');
            } else {
                $('#menuitem-template').attr('disabled', 'true');
            }
        });
    });

    $('.delete-item').click( function(e) {
        var itemId = $(this).attr('data-id');

        Swal.fire({
            title: 'Delete menu item?',
            text: 'This (and all sub-menu items) will be removed. This cannot be undone.',
            showCancelButton: true,
            confirmButtonText: `Delete`,
            type: 'warning'
          }).then((result) => {
            if (result.value == true) {
                $.post('/menu/menu-item/delete?id=' + itemId, function (data) {
                    // @todo: maybe some error handling?
                    var target = '#menuItem_' + itemId;
                    Swal.fire('Deleted!', 'Menu item (and all sub-menu items) removed!', 'success');
                    $(target).hide('slow', function() { 
                        $(target).remove(); 
                    });
                });
            } else {
              Swal.fire('Delete cancelled', '', 'info');
            }
          });
    });
  });