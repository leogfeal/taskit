var dataTableColumns = [
    { "data": "name" },
    { "data": "username" },
    { "data": "rol" },
    { "data": "email" },
    { "data": "phone" },
    { "data": "actions" }
 ]; 
var dataTableUrlAjax = Routing.generate('admin_get_ajax_users');
var urlInfoObject = Routing.generate('admin_get_ajax_user');
var msgDelete = 'Do you want to delete this user?';
var msgView = 'User details';
var index_action = 4;