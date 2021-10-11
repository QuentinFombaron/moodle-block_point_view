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
 * Defines the behavior of the overview (reactions details) page of a Point of View block.
 * @package    block_point_view
 * @copyright  2020 Quentin Fombaron, 2021 Astor Bizard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    return {
        init: function() {
            // Create an accordion home-made table.
            $('.row_module').each(function() {
                var $detailsrow = $(this).next('.row_module_details');

                $(this).find('.c6')
                .click(function() {
                    $detailsrow.toggle();
                    $(this).find('i').toggleClass('fa-caret-right fa-caret-down');
                })
                .find('i').show();
            });

            // Create two views : Integer and Percentage, both visible on click.
            $('.reactions-col').click(function() {
                $('.voteInt, .votePercent').toggle();
            });
        }
    };
});