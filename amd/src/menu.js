/* Include JQuery */
define(['jquery'], function($) {
    return {
        init: function() {
            var openRow;
            /**
             *
             * @param event
             */
            function rowDetails(event) {
                if (openRow === event.data.id) {
                    $('.row_' + event.data.id + '_details').css({'display': 'none'});
                    openRow = null;
                } else {
                    $('.row_' + event.data.id + '_details').css({'display': ''});
                    openRow = event.data.id;
                }
            }

            $(function() {
                for (var i = 1; i <= 4; i++) {
                    $('.row_' + i + '_details').css({'display': 'none'});
                    $('.row_' + i + ' .c3').click({id: i}, rowDetails);
                }
            });
        }
    };
});