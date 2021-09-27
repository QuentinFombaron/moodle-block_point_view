define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    return {
        init: function(envconf, trackcolors) {

            var $enableReactions = $('#id_config_enable_point_views');
            var $enableDifficultyTracks = $('#id_config_enable_difficultytracks');

            function updateElementsVisibility() {
                var reactionsEnabled = $enableReactions.val() > 0;
                var difficultyTracksEnabled = $enableDifficultyTracks.val() > 0;

                $('#id_activities_header').toggle(reactionsEnabled || difficultyTracksEnabled);

                $('.reactions, #id_images_header').toggle(reactionsEnabled);
                $('.difficultytracks').toggle(difficultyTracksEnabled);
            }

            updateElementsVisibility();

            $enableReactions.change(updateElementsVisibility);
            $enableDifficultyTracks.change(updateElementsVisibility);

            function updateGlobalButtonsFor(sectionOrType) {
                var $checkboxes = $('.cb' + sectionOrType + ':checkbox');
                var nBoxesChecked = $checkboxes.filter(':checked').length;
                $('#enableall' + sectionOrType).toggleClass('active', nBoxesChecked === $checkboxes.length);
                $('#disableall' + sectionOrType).toggleClass('active', nBoxesChecked === 0);
            }

            // Reactions for a module checkbox change.
            $('.enablemodulereactions').change(function() {
                updateGlobalButtonsFor($(this).data('type')); // Update Enable/Disable buttons state for module type.
                updateGlobalButtonsFor($(this).data('section'));  // Update Enable/Disable buttons state for section.
            });

            $('.enable-disable button').each(function() {
                var sectionOrType = $(this).data('type') || $(this).data('section');

                updateGlobalButtonsFor(sectionOrType); // Update Enable/Disable buttons state on page load.

                $(this).click(function() {
                    $('.cb' + sectionOrType + ':checkbox')
                    .prop('checked', $(this).data('enable')) // Update all corresponding checkboxes.
                    .change(); // Trigger a change to update Enable/Disable buttons state accordingly.
                });
            });

            // Difficulty track change.
            $('.moduletrackselect select').change(function() {
                $('#track_' + $(this).data('id')).css({
                    'background-color': trackcolors[$(this).val()] // Change track color.
                });
            }).change(); // Update track colors once on page load.

            function buttonWithAjaxCall($button, message, ajaxmethod, ajaxargs, callback) {
                $button.click(function(e) {
                    M.util.show_confirm_dialog(e, {
                        message: M.util.get_string(message, 'block_point_view'),
                        callback: function() {
                            ajax.call([
                                {
                                    methodname: 'block_point_view_' + ajaxmethod,
                                    args: ajaxargs,
                                    done: callback,
                                    fail: notification.exception
                                }
                            ]);
                        }
                    });
                });
            }

            buttonWithAjaxCall(
                    $('#delete_custom_pix'),
                    'deleteemojiconfirmation',
                    'delete_custom_pix',
                    {
                        contextid: envconf.contextid,
                        courseid: envconf.courseid,
                        draftitemid: $('input[name="config_point_views_pix"]').val()
                    },
                    function() {
                        $('.custom-pix-preview, #delete_custom_pix').remove(); // Remove emoji preview and button.
                        // For an unknown reason, the following instruction with jQuery does not work
                        // (or at least does not trigger the expected listener).
                        document.querySelector('#fitem_id_config_point_views_pix .fp-path-folder-name').click();
                    }
            );

            buttonWithAjaxCall(
                    $('#reset_reactions'),
                    'resetreactionsconfirmation',
                    'update_db',
                    {
                        func: 'reset',
                        courseid: envconf.courseid
                    },
                    function() {
                        notification.alert(M.util.get_string('info', 'moodle'),
                                M.util.get_string('reactionsresetsuccessfully', 'block_point_view'),
                                M.util.get_string('ok', 'moodle'));
                    }
            );

            $('[name=config_pixselect]').change(function() {
                var newsource = $(this).val();
                $('img.currentpix').each(function() {
                    var $img = $('img[data-source="' + newsource + '"][data-reaction="' + $(this).data('reaction') + '"]');
                    if ($img.length == 1 && $img.attr('src') > '') {
                        $(this).attr('src', $img.attr('src'));
                    }
                });
            });
        }
    };
});