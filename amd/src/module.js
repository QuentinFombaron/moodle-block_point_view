define(['jquery'], function ($) {
    return {
        init: function (idmax, types) {
            /**
             *
             * @param {array} event
             */
            function treatEnableForm(event) {
                $('.check_section_' + event.data.id).prop('checked', true);
                $(this).addClass('active');
                $('#id_disable_' + event.data.id).removeClass('active');
            }

            /**
             *
             * @param {array} event
             */
            function treatDisableForm(event) {
                $('.check_section_' + event.data.id).prop('checked', false);
                $(this).addClass('active');
                $('#id_enable_' + event.data.id).removeClass('active');
            }

            /**
             *
             * @param string
             * @returns {string}
             */
            function jsUcfirst(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            for (var i = 2; i <= idmax; i++) {
                $('#id_enable_' + i).click({id: i}, treatEnableForm);
                $('#id_disable_' + i).click({id: i}, treatDisableForm);
            }

            types.forEach(function(type) {
                $('#id_enable' + jsUcfirst(type) + 's').click(function() {
                    /* Check all checkbox of $type */
                    $('input.' + type).prop('checked', true);
                    /* Make the button darker without disable it */
                    $(this).addClass('active');
                    /* And reset the other one to see that this one was cliked */
                    $('#id_disable' + jsUcfirst(type) + 's').removeClass('active');
                });

                $('#id_disable' + jsUcfirst(type) + 's').click(function() {
                    $('input.' + type).prop('checked', false);
                    $(this).addClass('active');
                    $('#id_enable' + jsUcfirst(type) + 's').removeClass('active');
                });
            });
        }
    };
});