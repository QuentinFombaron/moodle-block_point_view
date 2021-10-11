// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the behavior of the configuration page of a Point of View block.
 * @package    block_point_view
 * @copyright  2020 Quentin Fombaron, 2021 Astor Bizard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {

    /**
     * Manage updating of visibility state of elements related to Reactions and Difficulty tracks,
     * depending on whether they are enabled or not.
     */
    function manageElementsVisibility() {
        var $enableReactions = $('#id_config_enable_point_views');
        var $enableDifficultyTracks = $('#id_config_enable_difficultytracks');

        var updateElementsVisibility = function() {
            var reactionsEnabled = $enableReactions.val() > 0;
            var difficultyTracksEnabled = $enableDifficultyTracks.val() > 0;

            $('#id_activities_header').toggle(reactionsEnabled || difficultyTracksEnabled);

            $('.reactions, #id_images_header').toggle(reactionsEnabled);
            $('.difficultytracks').toggle(difficultyTracksEnabled);
        };

        updateElementsVisibility();

        $enableReactions.change(updateElementsVisibility);
        $enableDifficultyTracks.change(updateElementsVisibility);
    }

    /**
     * Manage Enable/Disable buttons for sections and module types.
     */
    function manageEnableDisableButtons() {

        // Update of Enable/Disable buttons state for a section or module type,
        // depending on checkboxes state for that section or module type.
        var updateEnableDisableButtonsFor = function(sectionOrType) {
            var $checkboxes = $('.cb' + sectionOrType + ':checkbox');
            var nBoxesChecked = $checkboxes.filter(':checked').length;
            $('#enableall' + sectionOrType).toggleClass('active', nBoxesChecked === $checkboxes.length);
            $('#disableall' + sectionOrType).toggleClass('active', nBoxesChecked === 0);
        };

        $('.enablemodulereactions').change(function() {
            updateEnableDisableButtonsFor($(this).data('type')); // Update Enable/Disable buttons state for module type.
            updateEnableDisableButtonsFor($(this).data('section'));  // Update Enable/Disable buttons state for section.
        });

        $('.enable-disable button').each(function() {
            var sectionOrType = $(this).data('type') || $(this).data('section');

            updateEnableDisableButtonsFor(sectionOrType); // Update Enable/Disable buttons state on page load.

            $(this).click(function() {
                $('.cb' + sectionOrType + ':checkbox')
                .prop('checked', $(this).data('enable')) // Update all corresponding checkboxes.
                .change(); // Trigger a change to update Enable/Disable buttons state accordingly.
            });
        });
    }

    /**
     * Adds a listener to a button click with a confirm dialog and ajax call.
     * @param {jQuery} $button The button to add the listener to.
     * @param {String} message Confirmation message, a string component of block_point_view.
     * @param {String} ajaxmethod Ajax method to be called.
     * @param {Object} ajaxargs Arguments to be passed to the ajax call.
     * @param {Function} callback A function called after ajax call completed successfully.
     */
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

    return {
        init: function(envconf, trackcolors) {

            manageElementsVisibility();

            manageEnableDisableButtons();

            // Difficulty track change.
            $('.moduletrackselect select').change(function() {
                $('#track_' + $(this).data('id')).css({
                    'background-color': trackcolors[$(this).val()] // Change track color.
                });
            }).change(); // Update track colors once on page load.

            // Custom emoji deletion.
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
                        $('.pix-preview[data-source="custom"], #delete_custom_pix').remove(); // Remove emoji preview and button.
                        // Refresh draft area files.
                        // # For an unknown reason, the following instruction with jQuery does not work
                        // # (or at least does not trigger the expected listener).
                        document.querySelector('#fitem_id_config_point_views_pix .fp-path-folder-name').click();
                    }
            );

            // Update current emoji on emoji change.
            $('[name=config_pixselect]').change(function() {
                var newsource = $(this).val();
                $('img.currentpix').each(function() {
                    var $img = $('img[data-source="' + newsource + '"][data-reaction="' + $(this).data('reaction') + '"]');
                    if ($img.length == 1 && $img.attr('src') > '') {
                        $(this).attr('src', $img.attr('src'));
                    }
                });
            });

            // Course reactions reset.
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
        }
    };
});