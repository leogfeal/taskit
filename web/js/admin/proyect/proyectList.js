var dataTableColumns = [
    { "data": "name" },
    { "data": "description" },
    { "data": "created_time" },
    { "data": "enabled" },
    { "data": "enabled_disable" },
    { "data": "actions" }
 ]; 
var dataTableUrlAjax = Routing.generate('admin_get_ajax_proyects');
var urlInfoObject = Routing.generate('admin_get_ajax_proyect');
var msgDelete = 'Do you want to delete this project?';
var msgView = 'Project details';
var index_action = 3;