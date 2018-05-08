var proyect = function () {
    return {
        init: function () {
            eventDraggable();
            eventDeleteUserAssigned();
        },
        onValidate: function (){
            $('form').validate({
                errorPlacement: function(error, element) {
                    $(element).parent().parent().find('i.e-validate').remove();
                    $(element).next('i').remove();
                    $(element).parent().addClass('has-error');
                    $(element).parent().parent().find('label:first').before('<i class="e-validate text-danger fa fa-user-times fa-fw hidden-sm hidden-xs" title="' + $(error).html() + '"></i> ');
                    $(element).after('<i class="e-validate text-danger hidden-md hidden-lg">' + $(error).html() + '</i>');
                    $('i.hidden-sm').tooltip({'placement': 'bottom'});
                },
                errorElement: 'i',
                errorClass: 'e-validate',
                rules: {
                    'appbundle_proyect[name]': 'required',
                },
                messages: {
                    'appbundle_proyect[name]': Translator.trans('field.required', {}, 'validators'),

                },
                unhighlight: function(element) {
                    $(element).parent().parent().find('i.e-validate').remove();
                    $(element).next('i').remove();
                    $(element).parent().removeClass('has-error');
                }
            });
        },
        initProyects: function (){
            eventShowModalDetails();
            eventChangeStatus();
        },
        settings: {}
    };


    function eventShowModalDetails(){
        $(document).on('click', 'span.Enabled-Disable', function (e) {
            var span_ids = $(this).attr('id').split('-');
            var proyect_id = span_ids[1];
            var status = (span_ids[2] == 0)?'Disable':'Enable';
            var name_button = 'buttonChangeStatus-'+span_ids[1]+'-'+span_ids[2];
            $('#a-modalChangeStatus').attr('name',name_button);
            $('#a-modalChangeStatus').html(status);
            $('#modalViewStatus').modal('show');
            $('#modalViewLabelStatus').html('Proyect');
            $('.modal-loading').show();
            $('.ajax-info').empty();
            $.get(Routing.generate('admin_get_ajax_proyect_change_status'), {
                'id': proyect_id
            }, function(result) {
                $('.modal-loading').hide();
                $('.ajax-info').html(result);

            });
        });
    }

    function eventChangeStatus(){
        $(document).on('click', '#a-modalChangeStatus', function (e) {
            var button_ids = $(this).attr('name').split('-');
            var proyect_id = button_ids[1];
            var status = button_ids[2];
            $('.ajax-info').empty();
            $('.modal-loading').show();
            $.get(Routing.generate('admin_get_ajax_proyect_save_change_status'), {
                'id': proyect_id,
                'status': status
            }, function(result) {
                buildDatatable();
                $('#modalViewStatus').modal('hide');
            });
        });
    }

    function buildDatatable(){
        $('table#data-table').dataTable({
            "bDestroy": true
        }).fnDestroy();

        dataTable = $('table#data-table').DataTable({
            "language": {
                "lengthMenu": Translator.trans('table.textselectcount', {'var': '_MENU_'}, 'admin_js'),
                "zeroRecords": Translator.trans('table.noresult', {}, 'admin_js'),
                "info": Translator.trans('table.textnumberpage', {'page': '_PAGE_', 'pages': '_PAGES_', 'total': '_TOTAL_'}, 'admin_js'),
                "infoEmpty": Translator.trans('table.infoempty', {}, 'admin_js'),
                "search": Translator.trans('search', {}, 'admin_js'),
                "infoFiltered": Translator.trans('table.infofiltered', {'max': '_MAX_'}, 'admin_js'),
                "processing": Translator.trans('table.processing', {}, 'admin_js'),
                "paginate": {
                    "next": Translator.trans('next', {}, 'admin_js'),
                    "previous": Translator.trans('previous', {}, 'admin_js'),
                }
            },
            "columnDefs": [
                {"orderable": false, "targets": [parseInt(index_action)], "class": "action"},
            ],
            "processing": true,
            "serverSide": true,
            "method": 'POST',
            "ajax": {
                "url": Routing.generate('admin_get_ajax_proyects'),
            },
            "columns": dataTableColumns,
            "deferRender": true,
            "ordering": false,
        });

    }


    function addUserToForm(id){
        var ids = $('#appbundle_proyect_user_assigned').val();
        if(ids == '')
            ids = id;
        else{
            var newId = ','+id;
            ids+=newId;
        }
        $('#appbundle_proyect_user_assigned').val(ids);
    }

    function existUser(id){
        var ids = $('#appbundle_proyect_user_assigned').val();
        var list_ids = ids.split(',');
        var temp = false;
        for(var i=0; i<list_ids.length; i++){
            if(id == list_ids[i])
               temp = true;
        }
        return temp;
    }

    function deleteUserToForm(id){
        var ids = $('#appbundle_proyect_user_assigned').val();
        var list_ids = ids.split(',');
        var temp = '';
        for(var i=0; i<list_ids.length; i++){
            if(id != list_ids[i]){
                if(temp == '')
                    temp = list_ids[i];
                else{
                    temp = temp + ',' + list_ids[i];
                }
            }
        }
        $('#appbundle_proyect_user_assigned').val(temp);
    }

    function eventDeleteUserAssigned(){
        $(document).on('click', '.delete-user', function () {
            var a = $(this).parents('a.list-group-item');
            var a_id = a.attr('id');
            list_id = a_id.split('-');
            deleteUserToForm(list_id[1]);
            a.remove();
        });
    }

    function eventDraggable() {
        $(function(){
            $('.item').draggable({
                revert:true,
                proxy:'clone',
                onStartDrag:function(){
                    $(this).draggable('options').cursor = 'not-allowed';
                    $(this).draggable('proxy').css('z-index',10);
                },
                onStopDrag:function(){
                    $(this).draggable('options').cursor='move';
                }
            });
            $('.cart').droppable({
                onDragEnter:function(e,source){
                    $(source).draggable('options').cursor='auto';
                },
                onDragLeave:function(e,source){
                    $(source).draggable('options').cursor='not-allowed';
                },
                onDrop:function(e,source){
                    var id = $(source).find('p:eq(0)').html();
                    var name = $(source).find('p:eq(1)').html();
                    addUser(name, id);
                }
            });
        });
    }

    function addUser(name, id){
        if(!existUser(id)){
            idUser = 'user-'+id;
            var a = '<a class="list-group-item" id='+idUser+'> <i class="fa fa-user fa-fw"></i> '+name+' <span class="pull-right text-muted small delete-user " style="color: red; cursor: pointer"><em>Delete</em> </span> </a>';
            $('#id-selected-user').append(a);
            addUserToForm(id);
        }

    }

}();

