var dataTableColumns = [
    { "data": "proyect" },
    { "data": "name" },
    { "data": "state" },
    { "data": "user_assigned" },
    { "data": "user_created" },
    { "data": "start_time" },
    { "data": "end_time" },
    { "data": "priority" },
    { "data": "description" },
    { "data": "actions" }
 ];
var dataTableUrlAjax = Routing.generate('system_get_ajax_tasks_created');
var urlInfoObject = Routing.generate('admin_get_ajax_task');
var msgDelete = 'Do you want to delete this task?';
var msgView = 'Task details';
var index_action = 9;