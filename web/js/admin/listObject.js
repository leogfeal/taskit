$(document).ready(function() {
    dataTableGeneral = $('table#data-table').DataTable({
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
        "method": 'GET',
        "ajax": {
            "url": dataTableUrlAjax,
        },
        "columns": dataTableColumns,
        "deferRender": true,
        "ordering": false,
    });
});
function delObject(url, id) {
    $('#modalViewLabel').html(msgDelete);
    $('.modal-loading').show();
    $('.ajax-info').empty();
    $('#a-modalDelete').hide();
    $('#a-modalDelete').attr({'href': url});
    $.get(urlInfoObject, {
        'id': id
    }, function(result) {
        $('.modal-loading').hide();
        $('.ajax-info').html(result);
        $('#a-modalDelete').show();
    });
}
function viewObject(id) {
    $('#modalViewLabel').html(msgView);
    $('.modal-loading').show();
    $('.ajax-info').empty();
    $('#a-modalDelete').hide();
    $.get(urlInfoObject, {
        'id': id
    }, function(result) {
        $('.modal-loading').hide();
        $('.ajax-info').html(result);

    });
}