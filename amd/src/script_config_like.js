define(['jquery'], function($) {
    return {
        init: function(idmax, types, moduleids, trackcolor, courseid) {
            /* Shortcut to the "SAVE" button at the bottom of the page */
            $('#id_go_to_save').click(function() {
                window.location = '#id_submitbutton';
            }).removeClass('btn-secondary').addClass('btn-primary');

            /**
             * Management of the Enable/Disable all types button
             */
            function manageButtonGroup() {
                types.forEach(function(type) {
                    var checkedType = $('.' + type + ':checkbox:checked').length;
                    if ( checkedType === $('.' + type + ':checkbox').length) {
                        $('#id_enable' + jsUcfirst(type) + 's').addClass('active');
                        $('#id_disable' + jsUcfirst(type) + 's').removeClass('active');
                    } else if (checkedType === 0) {
                        $('#id_disable' + jsUcfirst(type) + 's').addClass('active');
                        $('#id_enable' + jsUcfirst(type) + 's').removeClass('active');
                    } else {
                        $('#id_enable' + jsUcfirst(type) + 's').removeClass('active');
                        $('#id_disable' + jsUcfirst(type) + 's').removeClass('active');
                    }
                });
            }

            /**
             * Management of the Enable/Disable all section button
             */
            function manageButtonSection() {
                for (var j = 2; j <= idmax; j++) {
                    var checkedBoxGroup = $('.checkboxgroup' + j + ':checkbox:checked').length;
                    if (checkedBoxGroup === $('.checkboxgroup' + j + ':checkbox').length) {
                        $('#id_enable_' + j).addClass('active');
                        $('#id_disable_' + j).removeClass('active');
                    } else if (checkedBoxGroup === 0) {
                        $('#id_disable_' + j).addClass('active');
                        $('#id_enable_' + j).removeClass('active');
                    } else {
                        $('#id_disable_' + j).removeClass('active');
                        $('#id_enable_' + j).removeClass('active');
                    }
                }
            }

            /**
             * Enable all activities in the section
             * @param {array} event
             */
            function treatEnableForm(event) {
                $('.check_section_' + event.data.id).prop('checked', true);
                $(this).addClass('active');
                $('#id_disable_' + event.data.id).removeClass('active');
                /* Update the buttons state */
                manageButtonGroup();
            }

            /**
             * Disable all activities in the section
             * @param {array} event
             */
            function treatDisableForm(event) {
                $('.check_section_' + event.data.id).prop('checked', false);
                $(this).addClass('active');
                $('#id_enable_' + event.data.id).removeClass('active');
                /* Update the buttons state */
                manageButtonGroup();
            }

            /**
             * Put the first character of a string in upper case
             * @param string A low case string
             * @returns {string} String with the first character in upper case
             */
            function jsUcfirst(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            /**
             *
             * @param event
             */
            function manageButton(event) {
                if (!$(this).is(':checked')) {
                    $('#id_enable_' + event.data.id).removeClass('active');
                    $('#id_enable' + jsUcfirst(event.data.type) + 's').removeClass('active');
                } else {
                    $('#id_disable_' + event.data.id).removeClass('active');
                    $('#id_disable' + jsUcfirst(event.data.type) + 's').removeClass('active');
                }

                var checkedBoxGroup = $('.checkboxgroup' + event.data.id + ':checkbox:checked').length;
                if (checkedBoxGroup === $('.checkboxgroup' + event.data.id + ':checkbox').length) {
                    $('#id_enable_' + event.data.id).addClass('active');
                } else if (checkedBoxGroup === 0) {
                    $('#id_disable_' + event.data.id).addClass('active');
                }

                var checkedType = $('.' + event.data.type + ':checkbox:checked').length;
                if ( checkedType === $('.' + event.data.type + ':checkbox').length) {
                    $('#id_enable' + jsUcfirst(event.data.type) + 's').addClass('active');
                } else if (checkedType === 0) {
                    $('#id_disable' + jsUcfirst(event.data.type) + 's').addClass('active');
                }
            }

            /**
             *
             * @param event
             */
            function selectChange(event) {
                var value;
                if (this.value !== undefined) {
                    value = parseInt(this.value);
                } else {
                    value = event.data.value;
                }

                if (value !== 0) {
                    var difficulty;
                    switch (value) {
                        case 1:
                            difficulty = trackcolor.greentrack;
                            break;
                        case 2:
                            difficulty = trackcolor.bluetrack;
                            break;
                        case 3:
                            difficulty = trackcolor.redtrack;
                            break;
                        case 4:
                            difficulty = trackcolor.blacktrack;
                            break;
                    }

                    (event.data.module).css({
                        'background-color': difficulty,
                        'color': 'white'
                    });
                } else {
                    (event.data.module).css({
                        'background-color': '',
                        'color': ''
                    });
                }
            }

            /**
             *
             */
            function checkConf() {
                if ($('#id_config_enable_likes_checkbox').is(':checked')
                    || $('#id_config_enable_difficulties_checkbox').is(':checked')) {
                    $('#id_activities').css({'display': ''});

                    if (!$('#id_config_enable_difficulties_checkbox').is(':checked')) {
                        moduleids.forEach(function(moduleId) {
                            $('#id_config_difficulty_' + moduleId).css({'display': 'none'});
                        });
                    } else {
                        moduleids.forEach(function(moduleId) {
                            $('#id_config_difficulty_' + moduleId).css({'display': ''});
                        });
                    }

                    if (!$('#id_config_enable_likes_checkbox').is(':checked')) {
                        $('#id_config_images').css({'display': 'none'});
                        moduleids.forEach(function(moduleId) {
                            $('#id_config_moduleselectm' + moduleId).css({'display': 'none'});
                        });
                    } else {
                        $('#id_config_images').css({'display': ''});
                        moduleids.forEach(function(moduleId) {
                            $('#id_config_moduleselectm' + moduleId).css({'display': ''});
                        });
                    }
                } else {
                    $('#id_activities').css({'display': 'none'});
                }
            }

            $('#id_close_field').click(function() {
                $('#id_activities').addClass('collapsed');
                window.location = '#maincontent';
            });

            /* TODO Commenter : Quand une checkbox est coché/décoché, je met à jour l'affachage des boutons Enable/Disable */
            moduleids.forEach(function(moduleId) {
                var classList = $('#id_config_moduleselectm' + moduleId).attr('class');
                if (classList !== undefined) {
                    var classes = classList.split(' ');
                    var type = null;
                    var id = null;
                    var types = ['book', 'chat', 'file', 'forum', 'glossary', 'page', 'quiz', 'resource', 'url', 'vpl', 'wiki'];
                    classes.forEach(function(className) {
                        if (types.indexOf(className) !== -1) {
                            type = types[types.indexOf(className)];
                        }
                        if (className.search('check_section_') !== -1) {
                            id = className.match(/\d+/);
                        }
                    });
                    $('#id_config_moduleselectm' + moduleId).click({id: id, type: type}, manageButton);
                }

                var value = parseInt($('#id_config_difficulty_' + moduleId + ' :selected').val());
                var idConfigDifficulty = $('#id_config_difficulty_' + moduleId);

                selectChange({data: {value: value, module: idConfigDifficulty}});

                idConfigDifficulty.change({module: idConfigDifficulty}, selectChange);
            });

            /* TODO Commenter : Les boutons Enable/Disable sont mis à jour au chargement de la page */
            for (var j = 2; j <= idmax; j++) {
                $('#id_enable_' + j).click({id: j}, treatEnableForm)
                    .removeClass('btn-secondary').addClass('btn-outline-success');
                $('#id_disable_' + j).click({id: j}, treatDisableForm)
                    .removeClass('btn-secondary').addClass('btn-outline-danger');
            }

            manageButtonSection();

            types.forEach(function(type) {
                $('#id_enable' + jsUcfirst(type) + 's').click(function() {
                    /* Check all checkbox of $type */
                    $('input.' + type).prop('checked', true);
                    /* Make the button darker without disable it */
                    $(this).addClass('active');
                    /* And reset the other one to see that this one was cliked */
                    $('#id_disable' + jsUcfirst(type) + 's').removeClass('active');
                    manageButtonSection();
                }).removeClass('btn-secondary').addClass('btn-outline-success');

                $('#id_disable' + jsUcfirst(type) + 's').click(function() {
                    $('input.' + type).prop('checked', false);
                    $(this).addClass('active');
                    $('#id_enable' + jsUcfirst(type) + 's').removeClass('active');
                    manageButtonSection();
                }).removeClass('btn-secondary').addClass('btn-outline-danger');
                manageButtonGroup();
            });

            /* Reset images buttun  */
            $('#id_config_reset_pix')
                .removeClass('btn-secondary')
                .addClass('btn-outline-warning')
                .click(function() {
                    $('#id_config_enable_pix_checkbox:checked').prop('checked', false);
                    $('#mform1').submit();
            });

            /* Hide fieldsets if Like or Difficulties checkboxes are disabled */
            checkConf();

            $('#id_config_enable_likes_checkbox').click(checkConf);
            $('#id_config_enable_difficulties_checkbox').click(checkConf);

            $('div[data-groupname="config_reset_confirm"]').css({'display': 'none'});

            $('#id_config_reset_yes')
                .removeClass('btn-secondary')
                .addClass('btn-success')
                .click(function() {
                    /* AJAX call to the PHP function which reset DB */
                    $.ajax({
                        type: 'POST',
                        url: '../blocks/like/update_db.php',
                        dataType: 'json',
                        data: {
                            func: 'reset',
                            userid: null,
                            courseid: courseid,
                            cmid: null,
                            vote: null
                        },

                        success: function() {
                            $('#mform1').submit();
                        }
                    });
                });

            $('#id_config_reset_no')
                .removeClass('btn-secondary')
                .addClass('btn-danger')
                .click(function() {
                    $('div[data-groupname="config_reset_confirm"]').css({'display': 'none'});
                });

            $('#id_config_reaction_reset_button')
                .removeClass('btn-secondary')
                .addClass('btn-outline-warning')
                .click(function() {
                    $('div[data-groupname="config_reset_confirm"]').css({'display': ''});
            });
        }
    };
});