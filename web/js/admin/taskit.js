var taskit = function () {
    return {
        getRowData: function (elem,table) {// get el row de un datatable para no tener que pasar el id en ninguna clase
            var current_row = $(elem).parents('tr');
            if (current_row.hasClass('child')) {//Check if the current row is a child row
                current_row = current_row.prev();//If it is, then point to the row before it (its 'parent')
            }
            var aData = $(table).DataTable().row(current_row).data();
            return aData;
        },
        getTextByEtiqueta: function (text) {// quitar todo las etiquetas html de un texto
            cleanText = text.replace(/<[^>]*>?/g, '');
            return cleanText;
        },
        validateSpecialCharacters: function(){ // agregarse a cualquier clase que necesite caracteres especiales
            $.validator.addMethod("specialCharacters", function(value, element) {
                var validate = true;
                var characters = '\/:*?<>|$#@&^%';
                for(var i=0; i < characters.length; i++ ) {
                    if(value.indexOf(characters.charAt(i)) !== -1) {
                        validate = false;
                        break;
                    }
                }
                return validate;
            }, "Letters, numbers, and underscores only please");
        },
        validateSpecialCharacters1: function(){ // agregarse a cualquier clase que necesite caracteres especiales
            $.validator.addMethod("specialCharacters1", function(value, element) {
                var validate = true;
                var characters = '\/:*?<>|$#@&^% ';
                for(var i=0; i < characters.length; i++ ) {
                    if(value.indexOf(characters.charAt(i)) !== -1) {
                        validate = false;
                        break;
                    }
                }
                return validate;
            }, "Letters, numbers, and underscores only please");
        },
        eventExpandAccordion: function(){//evento para que cambie el accordion cuando expanda y se recoja
            $(".expand").on( "click", function() {
                $expand = $(this).find(">:first-child");

                if($expand.text() == "+") {
                    $expand.text("-");
                } else {
                    $expand.text("+");
                }
            });
        },
        settings: {}
    };


}();