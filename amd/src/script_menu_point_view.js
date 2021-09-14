define(['jquery'], function($) {
    return {
        init: function() {
            /* Create an accordeon home made table */
            $('.row_module').each(function() {
                var $detailsrow = $(this).next('.row_module_details');

                $detailsrow.hide();
                $(this).find('.c6')
                .click(function() {
                    $detailsrow.toggle();
                    $(this).find('i').toggleClass('fa-caret-right fa-caret-down');
                })
                .find('i').show();
            });

            /* Create two views : Integer and Percentage, both visible on click */
            $('.reactions-col').click(function() {
                $('.voteInt, .votePercent').toggle();
            });
        }
    };
});