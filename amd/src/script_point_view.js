// Include JQuery
define(['jquery', 'core/ajax', 'core/notification'], function($, ajax, notification) {
    
    function callOnModulesListLoad(call) {
        call();
        
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (typeof(settings.data) !== 'undefined') {
                var data = JSON.parse(settings.data);
                if (data.length > 0 && typeof(data[0].methodname) !== 'undefined') {
                    if (data[0].methodname === 'format_tiles_get_single_section_page_html') {
                        call();
                    }
                }
            }
        });
    }
    
    function setUpDifficultyTracks(difficultyLevels, trackColors) {
        difficultyLevels.forEach(function(module) {
            var $track = $('<div class="block_point_view track"></div>')
            .css({
                'background-color': trackColors[parseInt(module.difficultyLevel)]
            });
            // Decide where to put the track.
            var $container = $('#module-' + module.id + ' .mod-indent-outer');

            // Add the track.
            if ($container.find('.block_point_view.track').length === 0) {
                $container.prepend($track);
            }
            // If there is indentation, move the track after it.
            $container.find('.mod-indent').after($track);
            
        });
    }
    
    return {
        init: function(courseId) {

            // Wait that the DOM is fully loaded
            $(function() {
                
                var blockData = $('.block.block_point_view .block_point_view_data').data('blockdata');

                callOnModulesListLoad(function() {
                    setUpDifficultyTracks(blockData.difficultylevels, blockData.trackcolors);
                });
                
                // Enumeration of the possible reactions
                var Reactions = {
                        none: 0,
                        easy: 1,
                        better: 2,
                        hard: 3
                };

                //  Array of Reaction of the user for the activity
                var reactionVotedArray = {};
                
                /**
                 * Get a jQuery object in reaction zone for given module ID.
                 * @param {int} moduleId
                 * @param {String} selector (optional)
                 * @return {jQuery}
                 */
                function $get(moduleId, selector) {
                    var $element = $('#module-' + moduleId + ' .block_point_view.reactions-container');
                    if (typeof(selector) === 'undefined') {
                        return $element;
                    } else {
                        return $element.find(selector);
                    }
                }

                /**
                 * Function which modify the reaction group image in terms of kind of vote
                 * @param {int} moduleId
                 */
                function updateGroupImg(moduleId) {
                    // Build group image name.
                    var groupImg = 'group_';
                    $get(moduleId, '.reactionnb').each(function() {
                        if (parseInt($(this).text()) > 0) {
                            groupImg += $(this).data('reactionname').toUpperCase().charAt(0); // Add E, B or H.
                        }
                    });
                    // Modify the image source of the reaction group.
                    $get(moduleId, '.group_img').attr('src', blockData.pix[groupImg]);
                }
                
                function updateGroupNb(moduleId, nb) {
                    var $groupNbWrapper = $get(moduleId, '.group_nb');
                    var $groupNb = $groupNbWrapper.find('span');

                    var digits = Math.min(('' + nb).length, 5);
                    $groupNb
                    .text(nb)
                    .attr('title', M.util.get_string('totalreactions', 'block_point_view', nb))
                    .css({
                        // Adjust the size to fit in a fixed space (useful for the green dot).
                        'right': Math.max(0.25 * (digits - 2), 0) + 'em',
                        'transform': 'scaleX(' + (1.0 + 0.03*digits*digits - 0.35 * digits + 0.34) + ')'
                    });
                    
                    $groupNbWrapper
                    .toggleClass('novote', nb === 0)
                    .toggleClass('voted', reactionVotedArray[moduleId] !== Reactions.none);
                }

                $('.activity')
                .mouseover(function() {
                    $(this).css({
                        'background': 'linear-gradient(to right, rgba(0,0,0,0.04), rgba(0,0,0,0.04), transparent)',
                        'border-radius': '5px'
                    });
                })
                .mouseout(function() {
                    $(this).css({'background': ''});
                });

                // createReactions()
                callOnModulesListLoad(function() {
                    var htmlBlock = blockData.reactionstemplate;
                    
                    // For each selected module, create a reaction zone.
                    blockData.moduleswithreactions.forEach(function(module) {
                        var moduleId = parseInt(module.cmid);
                        var uservote = parseInt(module.uservote);

                        // Initialise reactionVotedArray.
                        reactionVotedArray[moduleId] = uservote;
                        
                        if ($('#module-' + moduleId).length === 1 && $get(moduleId).length === 0) {
                            var $htmlBlock = $(htmlBlock);
                            
                            $('#module-' + moduleId).prepend($htmlBlock);
                            
                            $htmlBlock.find('.reaction img').each(function() {
                                $(this).toggleClass('novote', parseInt(module['total' + $(this).data('reactionname')]) === 0);
                            });
                            $htmlBlock.find('.reactionnb').each(function() {
                                $(this)
                                .text(module['total' + $(this).data('reactionname')])
                                .toggleClass('nbselected', uservote === Reactions[$(this).data('reactionname')]);
                            });

                            updateGroupNb(moduleId, parseInt(module.totaleasy) + parseInt(module.totalbetter) + parseInt(module.totalhard));
                            manageReact(moduleId);
                        }
                    });
                });
                
                function getReactionImageSizeForRatio(ratio) {
                    return {
                        top: 15 - (10 * ratio),
                        left: 10 - (10 * ratio),
                        height: 20 * ratio
                    };
                }
                
                function getGroupImageSizeForRatio(ratio) {
                    return {
                        left: -10 + (10 * ratio),
                        height: 20 * ratio
                    };
                }
                
                function showReactions(moduleId) {
                    $get(moduleId, '.group_img')
                    .css({'pointer-events': 'none'})
                    .animate(getGroupImageSizeForRatio(0), 300)
                    .hide(0);
                    
                    $get(moduleId, '.group_nb').delay(50).hide(300);

                    $('#module-' + moduleId + ' .actions').delay(200).hide(300);

                    // Enable the pointer events for each reactions images
                    ['easy', 'better', 'hard'].forEach(function(reaction, index) {
                        var delay = 50 + 150 * index; // easy: 50, better: 200, hard: 350
                        
                        // Reactions images modifications to black and white if no reaction has been made
                        $get(moduleId, '.reaction img[data-reactionname="' + reaction + '"]')
                        .delay(delay).animate(getReactionImageSizeForRatio(1), 300)
                        .css({'pointer-events': 'auto'});
                        
                        $get(moduleId, '.reactionnb[data-reactionname="' + reaction + '"]')
                        .delay(delay+300)
                        .queue(function(next) {
                            $(this).addClass('shown');
                            next();
                        });
                    });
                }
                
                function hideReactions(moduleId) {
                    ['hard', 'better', 'easy'].forEach(function(reaction, index) {
                        var delay = 50 + 250 * index; // hard: 50, better: 300, easy: 550
                        $get(moduleId, '.reaction img[data-reactionname="' + reaction + '"]')
                        .css({'pointer-events': 'none'})
                        .delay(delay).animate(getReactionImageSizeForRatio(0), 500);
                        
                        $get(moduleId, '.reactionnb[data-reactionname="' + reaction + '"]')
                        .delay(delay)
                        .queue(function(next) {
                            $(this).removeClass('shown');
                            next();
                        });
                    });

                    updateGroupImg(moduleId);

                    // Show the reaction group image with nice animation.
                    $get(moduleId, '.group_img')
                    .delay(500)
                    .show(0)
                    .animate(getGroupImageSizeForRatio(1), 300)
                    .css({'pointer-events': 'auto'});

                    $get(moduleId, '.group_nb').delay(600).show(0);

                    $('#module-' + moduleId + ' .actions').delay(600).show(300);
                }
                
                function updateVoteNb(moduleId, reactionName, diff, selected) {
                    var $reactionNb = $get(moduleId, '.reactionnb[data-reactionname="' + reactionName + '"]');
                    var nbReaction = parseInt($reactionNb.text()) + diff;
                    $reactionNb
                    .text(nbReaction)
                    .toggleClass('nbselected', selected);

                    $get(moduleId, '.reaction img[data-reactionname="' + reactionName + '"]').toggleClass('novote', nbReaction === 0);
                    
                    updateGroupNb(moduleId, parseInt($get(moduleId, '.group_nb').find('span').text()) + diff);
                }
                
                function manageReactionChange(moduleId, reactionName) {
                 
                    var reactionSelect = Reactions[reactionName];

                    var previousReaction = reactionVotedArray[moduleId];
                    
                    var newVote = (reactionSelect === previousReaction) ? Reactions.none : reactionSelect;
                    
                    return ajax.call([
                        {
                            methodname: 'block_point_view_update_db',
                            args: {
                                func: 'update',
                                courseid: courseId,
                                cmid: moduleId,
                                vote: newVote
                            }
                        }
                    ])[0]
                    .done(function() {
                        reactionVotedArray[moduleId] = newVote; // Set current reaction.
                        if (previousReaction !== Reactions.none) {
                            // User canceled their vote (or updated to another one).
                            var previousReactionName = ['', 'easy', 'better', 'hard'][previousReaction];
                            updateVoteNb(moduleId, previousReactionName, -1, false);
                        }
                        if (newVote !== Reactions.none) {
                            // User added or updated their vote.
                            updateVoteNb(moduleId, reactionName, +1, true); // Add new vote.
                        }
                    })
                    .fail(notification.exception);
                }
                
                function manageReact(moduleId) {
                    updateGroupImg(moduleId);
                        
                    var reactionsVisible = false;
                    var groupTimeout = null;
                    var reactionsTimeout = null;
                    
                    var triggerHideReactions = function() {
                        reactionsTimeout = null;
                        reactionsVisible = false;
                        hideReactions(moduleId);
                    }
                    
                    var triggerShowReactions = function() {
                        groupTimeout = null;
                        reactionsVisible = true;
                        showReactions(moduleId);
                        clearTimeout(reactionsTimeout);
                        reactionsTimeout = setTimeout(triggerHideReactions, 2000);
                    }

                    // Group img click.
                    $get(moduleId, '.group_img')
                    .mouseover(function() {
                        $(this).stop().animate(getGroupImageSizeForRatio(1.15), 100);
                        groupTimeout = setTimeout(triggerShowReactions, 300);
                    })
                    .mouseout(function() {
                        if (!reactionsVisible) {
                            clearTimeout(groupTimeout);
                            $(this).stop().animate(getGroupImageSizeForRatio(1), 100);
                        }
                    })
                    .click(triggerShowReactions);
                    
                    var reactionsLock = false;
                    // Reaction img management
                    $get(moduleId, '.reaction img')
                    .mouseover(function() {
                        $(this).stop().animate(getReactionImageSizeForRatio(2), 100);
                    })
                    .mouseout(function() {
                        $(this).stop().animate(getReactionImageSizeForRatio(1), 100);
                    })
                    .click(function() {
                        // Use a mutex to avoid query / display inconsistencies.
                        // This is not a perfect mutex, but is actually enough for our needs.
                        if (reactionsLock === false) {
                            reactionsLock = true;
                            manageReactionChange(moduleId, $(this).data('reactionname'))
                            .done(function() {
                                reactionsLock = false;
                            });
                        }
                    });
                    
                    // Mouse out.
                    $get(moduleId, '.reactions')
                    .mouseout(function() {
                        clearTimeout(reactionsTimeout);
                        reactionsTimeout = setTimeout(triggerHideReactions, 1000);
                    })
                    .mouseover(function() {
                        clearTimeout(reactionsTimeout);
                    });
                }

                /* Dont' hide tooltip when reaction are in the top of course*/
                $('#region-main > .card').css({'overflow-x': 'unset'});
            });
        }
    };
});