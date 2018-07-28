var task = function () {
    return {
        init: function () {
            initDatapicker('#appbundle_task_end_time');
            initDatapicker('#appbundle_task_start_time');
        },
        initDropzone: function () {
            Dropzone.options.myDropzone = { // The camelized version of the ID of the form element
                // The configuration we've talked about above
                url:'#',
                previewsContainer: ".dz-message",
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 100,
                maxFiles: 100,
                addRemoveLinks: true,

                // The setting up of the dropzone
                init: function() {
                    var myDropzone = this;

                    // First change the button to actually tell Dropzone to process the queue.
                    this.element.querySelector("#btn-submit-form").addEventListener("click", function(e) {
                        // Make sure that the form isn't actually being sent.
                        if($('form').valid()){
                            $('.div-disable-content').show();
                            $('form').attr('action', task.settings.url_action);
                            if(myDropzone.getQueuedFiles().length == 0 )
                                $('form').submit();
                            else{
                                e.preventDefault();
                                e.stopPropagation();
                                myDropzone.processQueue();

                            }
                        }
                    });
                    this.on("addedfile", function(file) {
                        $('div.dz-progress').remove();
                    });
                    this.on("success", function(file) {
                        window.location.href = Routing.generate('system_tasks_created');
                    });
                }}
        },
        onValidate: function (){
            taskit.validateSpecialCharacters();
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
                    'appbundle_task[proyect]': 'required',
                    'appbundle_task[name]': {
                        required: true,
                        specialCharacters: true
                    },
                    'appbundle_task[state]': 'required',
                    'appbundle_task[end_time]': 'required',
                    'appbundle_task[start_time]': 'required',
                    'appbundle_task[priority]': 'required'
                },
                messages: {
                    'appbundle_task[proyect]': Translator.trans('field.required', {}, 'validators'),
                    'appbundle_task[name]': {
                        required: Translator.trans('field.required', {}, 'validators'),
                        specialCharacters: "Letters, numbers, and underscores only please"
                    },
                    'appbundle_task[state]': Translator.trans('field.required', {}, 'validators'),
                    'appbundle_task[end_time]': Translator.trans('field.required', {}, 'validators'),
                    'appbundle_task[start_time]': Translator.trans('field.required', {}, 'validators'),
                    'appbundle_task[priority]': Translator.trans('field.required', {}, 'validators')
                },
                unhighlight: function(element) {
                    $(element).parent().parent().find('i.e-validate').remove();
                    $(element).next('i').remove();
                    $(element).parent().removeClass('has-error');
                }
            });
        },
        initTasks: function () {
            initDaterangepicker();
            initMultiselect();
            btnSearch();
            fnViewModalNotes();
            fnShowModalCompleteTask();
            fnCompleteTask();
            fnShowModalDisapproveTask();
            fnDisapproveTask();
            eventDropdownMenuFrequency();
		},
	settings: {
            action:'',
            url_action:'',
            real_names_attached: new Array(),
            search_url:''
        }
    };
    
    function eventDropdownMenuFrequency(){
        $(document).on("click", ".dropdown-menu li", function () {
            var li = $(this);
            var button = $(this).parent().parent().find('button');
            var ul = $(this).parent();
            var a = $(this).find('a');
            var i = $(this).find('i');
            
            var i_class = i.attr('class');
            var table = '#data-table';
            var aData =  taskit.getRowData($(this),table);

            swal({
                title: "Are you sure?",
                text: "You want change frequency to "+$(this).find('a').text(),
                icon: "info",
                closeOnClickOutside: false,
                button: {
                    text: "OK",
                    closeModal: false,
                },

              })
              .then((willDelete) => {
                if (willDelete) { 
                    $.post(Routing.generate('system_change_frequency_task'), {
                    'frequency': a.attr('data-id'),
                    'task': aData.id
                    }, function(result) {
                        if(result.response){
                             swal.stopLoading();
                             swal.close();
                             swal("The frequency of the task has changed!!!", {
                                icon: "success",
                             });
                            button.html("<i class='fa fa-fw fa fa-circle "+i_class.replace('fa fa-fw fa fa-circle', '')+"'></i>  "+a.text()+"  <span class='caret'></span>");
                            ul.find('li').removeClass('disabled'); 
                            li.addClass('disabled');
                        }
                        else{
                            swal("Oops: An error has occurred!!!", {
                                icon: "error",
                            });
                        }
                    });     
                }
              });
        });
    }

    function fnShowModalDisapproveTask(){
        $(document).on("click", ".disapproveInCreateTask", function () {
            var table = '#data-table';
            var aData =  taskit.getRowData($(this),table);

            $('#p-desapprove-task').html('<b>Task: </b>'+taskit.getTextByEtiqueta(aData.name));
            $('#p-desapprove-assigned-to').html('<b>Assigned to: </b>'+taskit.getTextByEtiqueta(aData.user_assigned));

            $('.modal-loading-disapprove-task').hide();
            $('.info-disapprove-task').show();
            $('#notes-disapprove-task').val('');

            $('#task_id_disapprove').val(aData.id);
            $('#modalDisapproveTask').modal('show');
        });
    }

    function fnDisapproveTask(){
        $(document).on('click', '#a-modalDisapproveTask', function () {
            $('.info-disapprove-task').hide();
            $('.modal-loading-disapprove-task').show();

            $.post(Routing.generate('system_disapprove_task'), {
                'id': $('#task_id_disapprove').val(),
                'notes': $('#notes-disapprove-task').val()
            }, function(result) {
                if(result.response)
                    dataTableGeneral.ajax.reload();
                $('#modalDisapproveTask').modal('hide');
            });

        });

    }

    function fnShowModalCompleteTask(){
        $(document).on("click", ".completedInCreateTask", function () {
            var table = '#data-table';
            var aData =  taskit.getRowData($(this),table);

            $('#text-task').html('<h5>'+aData.name+'</h5>');
            $('#task_id').val(aData.id);
            $('#notes-complete-task').val('');

            $('#modalCompleteTask').modal('show');
        });
    }

    function fnCompleteTask(){
        $(document).on('click', '#a-modalCompleteTask', function () {
            $('.info-complete-task').hide();
            $('.modal-loading-completed-task').show();

            $.post(Routing.generate('system_completed_task'), {
                'id': $('#task_id').val(),
                'notes': $('#notes-complete-task').val()
            }, function(result) {
                if(result.response){
                    dataTableGeneral.ajax.reload();
                }
                $('#modalCompleteTask').modal('hide');
            });
        });
    }

    function fnShowPopover(){
        $('[data-toggle="popover"]').popover({
            placement : 'top',
            trigger : 'hover'
        });
    }
	
    function initDaterangepicker(){
		 $('#checkbox-createdOn').prop('checked', false);
		 $('.daterange').daterangepicker();
		 enableDisableCreatedOn();
		 eventCreatedOnDaterange();
	}
    
    function enableDisableCreatedOn(){
        if($('#checkbox-createdOn').is(':checked'))
          $('#filter_createdOn').prop('disabled', false);
        else
          $('#filter_createdOn').prop('disabled', true);
    }
    
    function eventCreatedOnDaterange(){
        $('#checkbox-createdOn').on('click', function (e) {
            enableDisableCreatedOn();
        });
    }
	
    function initMultiselect(){
            var msg_user = ['Select a user...', 'users'];
        ConfigMultiSelect('#filter_user_created_task', msg_user, 'empty');

    var msg_user = ['Select a user...', 'users'];
    ConfigMultiSelect('#filter_user_assigned_to', msg_user, 'empty');

            var msg_state = ['Select a status', 'status'];
        ConfigMultiSelect('#filter_states', msg_state, 'empty');

            var msg_proyect = ['Select a project', 'projects'];
        ConfigMultiSelect('#filter_proyect', msg_proyect, 'empty');
    }
	 
    function ConfigMultiSelect(id, text, idComponent) {
        $(id).multiselect({
            buttonWidth: '100%',
            maxHeight: 200,
            allSelectedText: Translator.trans('select.all', {}, 'admin_js'),
            numberDisplayed: 4,
            enableFiltering: true,
            filterPlaceholder: Translator.trans('search', {}, 'admin_js') + '...',
            buttonText: function (options, select) {
                if (options.length === 0) {
                    return Translator.trans(text[0], {}, 'admin_js');
                }
                else if (options.length > 4) {
                    var count = options.length;
                    var length = $(id + ' option').length;
                    var entity = Translator.trans(text[1], {}, 'admin_js');
                    return Translator.trans('selects.of.option', {'count': count, 'length': length, 'entity': entity}, 'admin_js');
                }
                else {
                    var labels = [];
                    options.each(function () {
                        if ($(this).attr('label') !== undefined) {
                            labels.push($(this).attr('label'));
                        }
                        else {
                            labels.push($(this).html());
                        }
                    });
                    return labels.join(', ') + '';
                }
            },
			onDropdownShow: function (event) {
                $(id + ' option').each(function () {
                    var input = $('label.checkbox input[value="' + $(this).val() + '"]');
                    input.prop('disabled', false);
                    input.parent('li').addClass('disabled');
                });
                if (idComponent != 'empty')
                    $('#' + idComponent + ' option:selected').each(function () {
                        var input = $('label.checkbox input[value="' + $(this).val() + '"]');
                        input.prop('disabled', true);
                        input.parent('li').addClass('disabled');
                    });
            }
        });
    }

    function initDatapicker(id)
    {
        date = new Date();
        moment.locale('en');
        if(id == '#appbundle_task_start_time'){
            $(id).datepicker({
                dateFormat: "mm-dd-yy",
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                dayNamesMin: moment.weekdaysMin(),
                monthNames: moment.months(),
                dayNames: moment.weekdays(),
                maxDate: 0,
            });
            if(task.settings.action == 'New')
                $( id ).datepicker( "setDate", date );

        }
        else{
            $(id).datepicker({
                dateFormat: "mm-dd-yy",
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                dayNamesMin: moment.weekdaysMin(),
                monthNames: moment.months(),
                dayNames: moment.weekdays(),
                minDate:0
            });
        }
        
    }
	
	function getFormSerialize(){
		return $('#filter-form').serialize();
	}

    function fnViewModalNotes(){
        var dataTableColumnsNotes = [
            { "data": "createdOn", title:"Created On" },
            { "data": "user" , title: "User"},
            { "data": "note", title:"Note"}
        ];
        $(document).on('click', 'button.btn-view-notes', function (e) {
            $('#modalViewNotes').modal('show');

            var btn_id = $(this).attr('id');
            var task_id = btn_id.split('-');

            $('div.ajax-info-notes').remove();
            $('div.my-body-notes').append("<div class='ajax-info-notes'><table id='data-table-notes' class='table table-striped table-hover dt-responsive display' cellspacing='0' width='100%'></table></div>");

            $('.modal-loading-notes').hide();
            dataTable = $('table#data-table-notes').DataTable({
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
                        "previous": Translator.trans('previous', {}, 'admin_js')
                    }
                },
                "columnDefs": [
                    {"orderable": false, "targets": [parseInt(index_action)], "class": "action"}
                ],
                "processing": true,
                "serverSide": true,
                "method": 'POST',
                "columns": dataTableColumnsNotes,
                "deferRender": true,
                "ordering": false
            });
        });
    }
	
	function btnSearch(){
        $('#a-modalFilter').on('click', function (e) {
             $('table#data-table').dataTable({
                "bDestroy": true
            }).fnDestroy();
            
            var value_created_on = '';
            if(!$('#checkbox-createdOn').is(':checked'))
                value_created_on = $('#filter_createdOn').val();
            
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
                    "url": task.settings.search_url+'?'+getFormSerialize()
                },
                "columns": dataTableColumns,
                "deferRender": true,
                "ordering": false,
            });
            
            $('#modalViewFilter').modal('hide');
        });
    }
}();

