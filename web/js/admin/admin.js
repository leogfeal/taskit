var Admin = function() {
    return {
        getCountsLeftMenu: function() {
            $.get(Routing.generate('admin_get_ajax_left_menu_count'),
                    function(data) {
                        if (data) {
                            for (key in data) {
                                $('.a-' + key).text(data[key]);
                            }
                        }
                    }
            );
        },
    };

}();