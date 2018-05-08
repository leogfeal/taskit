var Dashboard = function () {
    return {
        initZoneInformationProyects: function (data) {
            paintChart('.chart', data, 'Proyects');
            $('.highcharts-credits').remove();
        },
        initZoneInformationTasks: function (data) {
            $('.chart-task').html('');
            paintChart('.chart-task', data, 'Tasks');
            $('.highcharts-credits').remove();
        },
        initZoneTabManage: function () {
            fnShowModalCompleteTask();
            fnCompleteTask();

            fnShowModalDisapproveTask();
            fnDisapproveTask();
        },
        initZoneTabMyTask: function(){
            fnShowModalResolvedTask();
            fnResolvedTask();
            fnEditResolvedTask();
        },
        settings: {
            show_chart_task:false
        }
    };

    function fnGetAllTaskInZoneInformation(task){
        var info = "<i class='m-l-1 fa fa-fw fa-circle' style='color: "+task.color+"'></i> <span class='text-white m-r-1'> "+task.amount+" </span> "+ task.name +" <br>";
        return info;
    }

    function fnGetTrAllTaskInZoneInformation(task){
        var tr = "<tr>"+
                    "<td class='text-white b-a-0'>"+ task.name +"</td>"+
                    "<td class='text-right b-a-0'><span class='label label-outline' style='background-color: "+ task.color +"'>0</span></td>"+
                 "</tr>";
        return tr;
    }

    function fnGetTrMyTaskInZoneInformation(task){
        var tr = "<tr>"+
                    "<td class='text-white b-a-0'>"+ task.name_my_task +"</td>"+
                    "<td class='text-right b-a-0'><span class='label label-outline' style='background-color: "+ task.color +"'>"+task.amount+"</span></td>"+
                 "</tr>";
        return tr;
    }

    function fnUpdateZoneInformationTask(){
        $.post(Routing.generate('system_ajax_get_all_task_dashboard'), {}, function(tasks) {
            if(Dashboard.settings.show_chart_task){
                $('.zone-information-all-task-show-chart-task').html('');
                var data = [];
                for(var i=0; i<tasks.length; i++){
                    $('.div-disable-content-all-task').show();
                    $('.zone-information-all-task-show-chart-task').append(fnGetAllTaskInZoneInformation(tasks[i]));
                    var obj = {
                        'name': tasks[i].name,
                        'y': tasks[i].amount,
                        'color': tasks[i].color
                    };
                    data.push(obj);
                }
                Dashboard.initZoneInformationTasks(data);
            }
            else{
                $('#zone-information-all-task-table tr').remove();
                for(var i=0; i<tasks.length; i++){
                    $('.div-disable-content-all-task').show();
                    $('#zone-information-all-task-table').append(fnGetTrAllTaskInZoneInformation(tasks[i]));
                }

            }
            $('.div-disable-content-all-task').hide();
        });
        //------------------ZONE INFORMATION MYTASK-----------------------------------------------------------------------
        $.post(Routing.generate('system_ajax_get_my_task_dashboard'), {}, function(tasks) {
            $('.div-disable-content-all-task').show();
            $('#zone-information-my-task-table tr').remove();
            for(var i=0; i<tasks.length; i++){
                $('.div-disable-content-all-task').show();
                $('#zone-information-my-task-table').append(fnGetTrMyTaskInZoneInformation(tasks[i]));
            }

            $('.div-disable-content-all-task').hide();
        });

    }

    function fnEditResolvedTask(){
        $(document).on('click', '#a-modalEditResolvedTask', function () {
            $('.info-resolved-task').hide();
            $('.modal-loading-resolved').show();
            window.location.href = Routing.generate('system_task_assigned_resolved', {id:$('#modalIdResolvedTask').val(), 'default':1});
        });
    }

    function fnShowModalResolvedTask(){
        $(document).on('click', '.resolved-task', function () {
            $('.modal-loading-resolved').hide();
            $('.info-resolved-task').show();
            var id_button = $(this).attr('id');
            var task_id = id_button.split('-');

            $('#modalIdResolvedTask').val(task_id[1]);
            $('#modalResolvedTask').modal('show');
        });
    }

    function fnResolvedTask(){
        $(document).on('click', '#a-modalResolvedTask', function () {
            $('.info-resolved-task').hide();
            $('.modal-loading-resolved').show();
            $('.div-disable-content-all-task').show();
            $.post(Routing.generate('system_ajax_task_assigned_resolved'), {
                'id': $('#modalIdResolvedTask').val()
            }, function(result) {
                if(result){
                    fnUpdatePanelReadyFTInMyTask();
                    fnUpdateZoneInformationTask();
                    fnUpdatePanelReadyFTInTabManager();
                    var tr = $('#resolvedTask-'+$('#modalIdResolvedTask').val()).parent().parent().parent();
                    tr.remove();

                    amount_task = parseInt($('span.amount-task-pending').html()) - 1;
                    $('span.amount-task-pending').html(amount_task);

                    if(amount_task == 0){
                        var div = "<div class='alert alert-warning' style='margin-bottom: auto;'>"+
                            "<i class='fa fa-warning'></i> There are no tasks assigned to you"+
                            "</div>";
                        $('.no-bg-pending').append(div);
                    }
                    $('#modalResolvedTask').modal('hide');
                }
                else
                    window.location.href = Routing.generate('login');

            });
        });
    }

    function getTrReadyForTestMyTask(description, task, proyect, userCreatedTask){
        var tr = "<tr title='"+description+"'>"+
                    "<td class='v-a-m'>"+
                        "<span class='task-name'>"+task+"</span><br>"+
                        "<span style='color: #000000' class='label label-cerulean label-outline'>"+proyect+"</span>"+
                    "</td>"+
                    "<td class='user-assigned'>"+
                        "<b>"+userCreatedTask+"</b>"+
                    "</td>"+
                 "</tr>";
        return tr;
    }

    function getTrReadyForTestTabManger(task){
        if(task.priority == 'Small')
            color = '#84b547';
        else if(task.priority == 'Medium')
            color = '#e76d3b';
        else
            color = '#cc3e4a';

        var date =  moment(task.createdOn.date);
        var now = moment();
        var diff = now.diff(date,'days');
        var routing_task_edit = Routing.generate('system_task_edit', {id:task.task_id, 'default':1});


        var tr = "<tr title='"+task.description+"'>"+
            "<td class='v-a-m'> <div style='display: flex'> <i class='fa fa-fw fa fa-circle' style='margin-top: 2%; color:"+color+"'></i> "+task.priority+"</div></td>"+
            "<td class='v-a-m'> <span class='task-name'>"+task.task+"</span> <br> " +
            "<span style='color: #000000' class='label label-cerulean label-outline'>"+ task.proyect +"</span>" +
            "<input type='hidden' value='"+ task.project_id +"' class='task-project-name'></td>" +
            "<td>"+date.format('MM/DD/YYYY')+" <b>Days: " +diff+"</b></td>" +
            "<td class='user-assigned'><b>"+task.userAssigned+"</b></td>" +
            "<td class='text-right v-a-m'> <div class='form-group-sm' style='display: inline-flex;'> " +
            "<button id='completeTask-"+ task.task_id +"' title='Completed Task' class='btn btn-xs btn-default completed-task'><i class='fa fa-check fa-fw'></i></button>" +
            "<a href='"+routing_task_edit+"' title='Edit' class='btn btn-xs btn-default'><i class='fa fa-pencil fa-fw'></i></a>" +
            "<button id='disapproveTask-"+task.task_id+"' title='Disapprove Task' class='btn btn-xs btn-default disapprove-Task'><i class='fa fa-times-circle fa-fw'></i></button>" +
            "<a href='#' title='Details' onclick='viewObject("+task.task_id  +")' class='btn btn-xs btn-default' data-toggle='modal' " +
            "data-target='#modalView'><i class='fa fa-search fa-fw'></i></a></div></td>" +
            "</tr>";

        return tr;
    }

    function getTrPendingTabMyTask(task){
        if(task.priority == 'Small')
            color = '#84b547';
        else if(task.priority == 'Medium')
            color = '#e76d3b';
        else
            color = '#cc3e4a';

        var date =  moment(task.createdOn.date);
        var now = moment();
        var diff = now.diff(date,'days');
        var routing_task_edit = Routing.generate('system_task_edit', {id:task.task_id, 'default':1});


        var tr = "<tr title='task.description'>"+
            "<td class='v-a-m'> <div style='display: flex'> <i class='fa fa-fw fa fa-circle' style='margin-top: 2%; color:"+color+"'></i> "+task.priority+"</div></td>"+
            "<td class='v-a-m'> <span class='task-name'>"+task.task+"</span> <br> " +
            "<span style='color: #000000' class='label label-cerulean label-outline'>"+ task.proyect +"</span>" +
            "<input type='hidden' value='"+ task.project_id +"' class='task-project-name'></td>" +
            "<td>"+date.format('MM/DD/YYYY')+" <b>Days: " +diff+"</b></td>" +
            "<td class='user-assigned'><b>"+task.userCreatedTask+"</b></td>" +
            "<td class='text-right v-a-m'> <div class='form-group-sm' style='display: inline-flex;'> " +
            "<button id='resolvedTask-"+ task.task_id +"' title='Resolved Task' class='btn btn-xs btn-default resolved-task'><i class='fa fa-check-circle fa-fw'></i></button>" +
            "<a href='#' title='Details' onclick='viewObject("+task.task_id  +")' class='btn btn-xs btn-default' data-toggle='modal' " +
            "data-target='#modalView'><i class='fa fa-search fa-fw'></i></a></div></td>" +
            "</tr>";

        return tr;
    }

    function fnUpdatePanelReadyFTInMyTask(){
        $('.modal-loading-panel-ready-for-test-myTask').show();
        $('#body-my-task-ready-for-test tr, .warning-panel-ready-for-test-mytask').remove();
        $.post(Routing.generate('system_ajax_get_task_ready_for_test_mytask'), {
            'id_status': 3
        }, function(tasks) {
            $('.modal-loading-panel-ready-for-test-myTask').hide();
            if(tasks.length > 0 ){
                amount = tasks.length;
                for(i = 0; i<amount; i++){
                    var tr = getTrReadyForTestMyTask(tasks[i].description,tasks[i].task, tasks[i].proyect, tasks[i].userCreatedTask);
                    $('#body-my-task-ready-for-test').append(tr);
                }
                $('span.amount-task-ready-for-test-mytask').html(amount);
            }
            else{
                var div = "<div class='alert alert-warning warning-panel-ready-for-test-mytask' style='margin-bottom: auto;'>" +
                            "<i class='fa fa-warning'></i> No existen tareas pendientes a revision creadas por usted." +
                          "</div>";
                $('.panel-ready-for-test-mytask').append(div);
                $('span.amount-task-ready-for-test-mytask').html(0);
            }
        });
    }

    function fnUpdatePanelReadyFTInTabManager(){
        $('.modal-loading-panel-ready-for-test-tab-manager').show();
        $('#body-tab-manager-ready-for-test tr, .warning-panel-ready-for-test-tab-manager').remove();
        $.post(Routing.generate('system_ajax_get_task_ready_for_test_tab_manager'), {}, function(tasks) {
            $('.modal-loading-panel-ready-for-test-tab-manager').hide();
            if(tasks.length > 0 ){
                length = tasks.length;
                for(i = 0; i<length; i++){
                    var tr = getTrReadyForTestTabManger(tasks[i])
                    $('#body-tab-manager-ready-for-test').append(tr);
                }
                $('span.amount-task-ready-for-test').html(length);
            }
        });
    }

    function fnUpdatePanelPendingTabMyTask(){
        $('.modal-loading-panel-pending-myTask').show();
        $('#body-my-task-pending tr, .warning-panel-pending-mytask').remove();
        $.post(Routing.generate('system_ajax_get_task_pending_tab_mytask'), {}, function(tasks) {
            $('.modal-loading-panel-pending-myTask').hide();
            if(tasks.length > 0 ){
                length = tasks.length;
                for(i = 0; i<length; i++){
                    var tr = getTrPendingTabMyTask(tasks[i])
                    $('#body-my-task-pending').append(tr);
                }
                $('span.amount-task-pending').html(length);
            }
        });
    }

    function fnShowModalCompleteTask(){
        $(document).on('click', '.completed-task', function () {
            var tr = $(this).parent().parent().parent();
            var task_name = tr.find('span.task-name')
            var task_project_name = tr.find('input.task-project-name');
            $('#text-task').html('<h5>'+task_name.html()+'</h5>');

            var id_button = $(this).attr('id');
            var task_id = id_button.split('-');
            $('.modal-loading-completed-task').hide();
            $('.info-complete-task').show();
            $('#notes-complete-task').val('');

            $('#task_id').val(task_id[1]);
            $('#project_name_modal').val(task_project_name.val());
            $('#modalCompleteTask').modal('show');

        });
    }

    function fnCompleteTask(){
        $(document).on('click', '#a-modalCompleteTask', function () {
            $('.info-complete-task').hide();
            $('.modal-loading-completed-task').show();
            $('.div-disable-content-all-task').show();
            $.post(Routing.generate('system_completed_task'), {
                'id': $('#task_id').val(),
                'notes': $('#notes-complete-task').val()
            }, function(result) {
                if(result.response){
                    fnUpdatePanelReadyFTInMyTask();
                    fnUpdateZoneInformationTask();
                    var tr = $('#completeTask-'+$('#task_id').val()).parent().parent().parent();
                    tr.remove();

                    updatePercentProject($('#project_name_modal').val())
                    amount_task = parseInt($('span.amount-task-ready-for-test').html()) - 1;
                    $('span.amount-task-ready-for-test').html(amount_task);

                    var length = $('#body-tab-manager-ready-for-test tr').length
                    if(length == 0){
                        var div = "<div class='alert alert-warning warning-panel-ready-for-test-tab-manager' style='margin-bottom: auto;'>" +
                            "<i class='fa fa-warning'></i> There are no pending tasks to review created by you." +
                            "</div>";
                        $('.panel-ready-for-test-tab-manager').append(div);
                    }
                }
                $('#modalCompleteTask').modal('hide');
            });
        });
    }

    function fnShowModalDisapproveTask(){
        $(document).on('click', '.disapprove-Task', function () {
            var tr = $(this).parent().parent().parent();
            var task_name = tr.find('span.task-name');
            var user_assigned = tr.find('td.user-assigned b');
            $('#p-desapprove-task').html('<b>Task: </b>'+task_name.html());
            $('#p-desapprove-assigned-to').html('<b>Assigned to: </b>'+user_assigned.html());

            var id_button = $(this).attr('id');
            var task_id = id_button.split('-');
            $('.modal-loading-disapprove-task').hide();
            $('.info-disapprove-task').show();
            $('#notes-disapprove-task').val('');

            $('#task_id_disapprove').val(task_id[1]);
            $('#modalDisapproveTask').modal('show');
        });
    }

    function fnDisapproveTask(){
        $(document).on('click', '#a-modalDisapproveTask', function () {
            $('.info-disapprove-task').hide();
            $('.modal-loading-disapprove-task').show();
            $('.div-disable-content-all-task').show();
            $.post(Routing.generate('system_disapprove_task'), {
                'id': $('#task_id_disapprove').val(),
                'notes': $('#notes-disapprove-task').val()
            }, function(result) {
                if(result.response){
                    fnUpdatePanelReadyFTInMyTask();
                    fnUpdatePanelPendingTabMyTask();

                    var tr = $('#disapproveTask-'+$('#task_id_disapprove').val()).parent().parent().parent();
                    tr.remove();

                    amount_task = parseInt($('span.amount-task-ready-for-test').html()) - 1;
                    $('span.amount-task-ready-for-test').html(amount_task);

                    var length = $('#body-tab-manager-ready-for-test tr').length
                    if(length == 0){
                        var div = "<div class='alert alert-warning warning-panel-ready-for-test-tab-manager' style='margin-bottom: auto;'>" +
                            "<i class='fa fa-warning'></i> There are no pending tasks to review created by you." +
                            "</div>";
                        $('.panel-ready-for-test-tab-manager').append(div);
                    }
                }
                fnUpdateZoneInformationTask();
                $('#modalDisapproveTask').modal('hide');
            });
        });
    }

    function updatePercentProject(project){
        var div = $('#project-'+project);
        span = div.find('span.task-completed').html();
        text1 = span.split(':');
        text2 = text1[1].split('/');
        value = parseInt(text2[0])+1;
        total = text2[1];
        percent = parseInt(parseInt(value) / parseInt(total) * 100);

        div.find('div.progress-bar').attr('aria-valuenow', percent);
        div.find('div.progress-bar').css('width', percent+'%');
        div.find('div.bar-percent').html(percent+'% Complete 60% Complete');
        div.find('span.task-completed').html('Task completed: '+value+'/'+total);
        div.find('span.task-percent').html(percent+'%');
    }

    function paintChart(selector, data, text){
        $(selector).highcharts({
            chart: {
                animation: false,
                type: 'pie',
                backgroundColor: null
            },
            title: {
                text: null
            },
            series: [{
                animation: {
                    duration: 750,
                    easing: 'easeOutQuad'
                },
                name: text,
                data: data,
                size: '90%',
                innerSize: '70%',
                dataLabels: {
                    formatter: function () {
                        return  null;
                    },
                    color: '#ffffff',
                    distance: -30
                }
            }]
        });
    };
}();