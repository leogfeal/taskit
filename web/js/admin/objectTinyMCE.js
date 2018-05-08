$(document).ready(function() {
    $("textarea").css("display", "block");
    var selector = "textarea";
    try {selector = ids_textareas;} catch (e) {}
        
    tinymce.init({
        selector: selector,
        plugins: [
            "advlist autolink lists link charmap print preview anchor",
            "searchreplace visualblocks code",
            "insertdatetime contextmenu paste wordcount"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | " +
                 "alignleft aligncenter alignright alignjustify | " +
                 "bullist numlist outdent indent | link image",
        language: "es",
        onchange_callback: function(editor) {
                tinymce.triggerSave();
                $("#" + editor.id).valid();
        }
    });
});