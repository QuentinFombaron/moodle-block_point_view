/* Include JQuery */
define(['jquery'], function($) {
    return {
        init: function(moduleIds) {
            var openRow = [];
            /**
             *
             * @param event
             */
            function rowDetails(event) {
                if (openRow[event.data.id]) {
                    $('.row_module' + event.data.id + '_details').css({'display': 'none'});
                    $('.row_module' + event.data.id + ' .c5').html('+');
                    openRow[event.data.id] = false;
                } else {
                    $('.row_module' + event.data.id + '_details').css({'display': ''});
                    $('.row_module' + event.data.id + ' .c5').html('-');
                    openRow[event.data.id] = true;
                }
            }

            $(function() {
                moduleIds.forEach(function(id) {
                    openRow[id] = false;
                    $('.row_module' + id + '_details').css({'display': 'none'});
                    $('.row_module' + id + ' .c5').click({id: id}, rowDetails);
                });

                var intDisplay = false;
                $('.voteInt').css({'display': 'none'});

                $('.c2, .c3, .c4').click(function() {
                    if (!intDisplay) {
                        $('.voteInt').css({'display': ''});
                        $('.votePercent').css({'display': 'none'});
                        intDisplay = true;
                    } else {
                        $('.votePercent').css({'display': ''});
                        $('.voteInt').css({'display': 'none'});
                        intDisplay = false;
                    }
                }).mouseout(function() {
                    $(this).css({'cursor': 'pointer'});
                });
            });
        }
    };
});