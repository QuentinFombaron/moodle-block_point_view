/* Include JQuery */
define(['jquery'], function($) {
    return {
        init: function(pointviewssql, moduleselect, difficultylevels, pix, envconf) {
            /* Wait that the DOM is fully loaded */
            $(function() {

                /* Resize the activity name field to give space to Likes icons */
                $('.mod-indent-outer').css({'width': '85%'});

                /* Array with all the needed data about the reactions of the page */
                var pointViewsSQL = pointviewssql;
                /* ID of the current user */
                var userId = parseInt(envconf.userid);

                var courseId = envconf.courseid;
                /* Array of the modules which have the reactions activated */
                var moduleSelect = moduleselect;

                /* Enumeration of the possible reactions */
                var Reactions = {
                    NULL: 0,
                    EASY: 1,
                    BETTER: 2,
                    HARD: 3
                };

                /* Array of boolean which determine if the mouse is over or not the reaction zone */
                var reactionArray = {};
                /*  Array of Reaction of the user for the activity */
                var reactionVotedArray = {};
                /* Array of total number of reaction for the activity */
                var totalVoteArray = {};
                /* Array of timer to see how long the mouse stay out of the reaction zone */
                var timerReactionsArray = {};
                /* Array of timer to see how long the mouse stay over the reaction group image */
                var timerGroupImgArray = {};

                /* Initialisation of the different arrays */
                moduleSelect.forEach(function(moduleIdParam) {
                    var moduleId = parseInt(moduleIdParam);
                    reactionArray[moduleId] = false;
                    reactionVotedArray[moduleId] = null;
                    totalVoteArray[moduleId] = null;
                    timerReactionsArray[moduleId] = null;
                    timerGroupImgArray[moduleId] = null;
                });

                /**
                 * Function which modify the reaction group image in terms of kind of vote
                 * @param {Object} module
                 * @param {int} moduleId
                 */
                function updateGroupImg(module, moduleId) {

                    /* Get the number of reaction for each one of it */
                    var easyVote = parseInt(module.getElementsByClassName('easy_nb')[0].innerText);
                    var betterVote = parseInt(module.getElementsByClassName('better_nb')[0].innerText);
                    var hardVote = parseInt(module.getElementsByClassName('hard_nb')[0].innerText);
                    var groupImg = 'group_';

                    /* Add the image suffix if there is at least 1 vote for the selected reaction */
                    if (easyVote) {
                        groupImg += 'E';
                    }
                    if (betterVote) {
                        groupImg += 'B';
                    }
                    if (hardVote) {
                        groupImg += 'H';
                    }

                    if (reactionVotedArray[moduleId] !== Reactions.NULL) {
                        var groupNb = $('#module-' + moduleId + ' .group_nb');
                        groupNb.addClass('voted');
                        if (totalVoteArray[moduleId] >= 10) {
                            groupNb.css({'font-size': '10px'});
                        }
                    } else {
                        $('#module-' + moduleId + ' .group_nb').removeClass('voted');
                    }

                    /* Modify the image source of the reaction group */
                    $('#module-' + moduleId + ' .group_img').attr('src', pix[groupImg]);
                }

                /**
                 * Function which returns the data for the moduleId in parameter
                 * @param {int} moduleId - ID of the module
                 * @returns {*} - Data for moduleId module
                 */
                function searchModule(moduleId) {

                    /* Boolean which check if the data are in pointViewsSQL array */
                    var assign = false;

                    /* Variable which will be returned */
                    var resultSearch;

                    pointViewsSQL.forEach(function(element) {
                        if (parseInt(element.cmid) === moduleId) {
                            resultSearch = element;
                            assign = true;
                        }
                    });

                    /* If the moduleId is not present in the array, there is no vote, so it creates an empty result */
                    if (!assign) {
                        resultSearch = {
                            'cmid': moduleId.toString(),
                            'courseid': courseId,
                            'total': '0',
                            'typeone': '0',
                            'typetwo': '0',
                            'typethree': '0',
                            'uservote': '0'
                        };
                    }

                    return resultSearch;
                }

                /**
                 * Event when the group image is mouse over
                 * @param {Object} event
                 */
                function groupImgMouseOver(event) {

                    /* Clear the animation queue to avoid image blinking */
                    $(this).stop();

                    /* Pointer modification to inform a possible click or interaction */
                    $(this).css({'cursor': 'pointer'});

                    /* Widen a little to inform that the image is mouse over */
                    $(this).animate({
                        top: -1.5,
                        left: -3,
                        height: 23
                    }, 100);

                    /* IF the mouse stay over at least 0.3 seconds */
                    timerGroupImgArray[event.data.moduleId] = setTimeout(function() {

                        /* Reactions images modifications to black and white if no reaction has been made */
                        if (parseInt((event.data.module).getElementsByClassName('easy_nb')[0].innerText) === 0) {
                            $('#module-' + (event.data.moduleId) + ' .easy')
                                .css({'-webkit-filter': 'grayscale(100%)', 'filter': 'grayscale(100%)'});
                        }
                        if (parseInt((event.data.module).getElementsByClassName('better_nb')[0].innerText) === 0) {
                            $('#module-' + (event.data.moduleId) + ' .better')
                                .css({'-webkit-filter': 'grayscale(100%)', 'filter': 'grayscale(100%)'});
                            // .attr('src', '../blocks/point_view/pix/better_BW.png');
                        }
                        if (parseInt((event.data.module).getElementsByClassName('hard_nb')[0].innerText) === 0) {
                            $('#module-' + (event.data.moduleId) + ' .hard')
                                .css({'-webkit-filter': 'grayscale(100%)', 'filter': 'grayscale(100%)'});
                        }

                        /*
                        * Hide the reaction group image with nice animation
                        * Completely hide the reaction group image to be sure
                        */
                        $('#module-' + (event.data.moduleId) + ' .group_img').animate({
                            top: '+=15',
                            left: '+=35',
                            height: 0
                        }, 300).hide(0);

                        /* Also hide the number of total reaction */
                        $('#module-' + (event.data.moduleId) + ' .group_nb').delay(50).hide(300);

                        $('#module-' + (event.data.moduleId) + ' .actions').hide(300);

                        /* Enable the pointer events for each reactions images */

                        /* After a short delay, show the 'Easy !' reaction image with nice animation */
                        $('#module-' + (event.data.moduleId) + ' .easy').delay(50).animate({
                            top: -15,
                            left: -20,
                            height: 20
                        }, 300)
                            .css({'pointer-events': 'auto'});

                        /* Also show the number of 'Easy !' reaction */
                        $('#module-' + (event.data.moduleId) + ' .easy_nb').delay(50).show(300);

                        /*
                        * After a delay, show the 'I'm getting better !' reaction image with nice
                        * animation
                        */
                        $('#module-' + (event.data.moduleId) + ' .better').delay(200).animate({
                            top: -15,
                            left: 25,
                            height: 20
                        }, 300)
                            .css({'pointer-events': 'auto'});

                        /* Also show the number of 'I'm getting better !' reaction */
                        $('#module-' + (event.data.moduleId) + ' .better_nb').delay(200).show(300);

                        /* After a delay, show the 'So Hard...' reaction image with nice animation */
                        $('#module-' + (event.data.moduleId) + ' .hard').delay(400).animate({
                            top: -15,
                            left: 70,
                            height: 20
                        }, 300)
                            .css({'pointer-events': 'auto'});

                        /* Also show the number of 'So Hard...' reaction */
                        $('#module-' + (event.data.moduleId) + ' .hard_nb').delay(400).show(300);
                    }, 500);

                    /* Reset timerReactions timer */
                    clearTimeout(timerReactionsArray[event.data.moduleId]);

                    /* IF the mouse stay over at least 3 seconds... */
                    timerReactionsArray[event.data.moduleId] = setTimeout(function() {

                        /* BUT the mouse is not in the reaction zone */
                        if (!(reactionArray[event.data.moduleId])) {

                            /*
                            * Disable the pointer events for each reactions images. This is to avoid a
                            * bug, because this is possible  select a reaction during the hiding and
                            * it create a bad comportment
                            */

                            /*
                            * After a short delay, hide the 'So Hard...' reaction image with nice
                            * animation
                            */
                            $('#module-' + (event.data.moduleId) + ' .hard').css({'pointer-events': 'none'})
                                .delay(50).animate({
                                top: -7.5,
                                left: 80,
                                height: 0
                            }, 500);

                            /* Also hide the number of 'So Hard...' reaction */
                            $('#module-' + (event.data.moduleId) + ' .hard_nb').delay(50).hide(300);

                            /*
                            * After a delay, show the 'I'm getting better !' reaction image with nice
                            * animation
                            */
                            $('#module-' + (event.data.moduleId) + ' .better').css({'pointer-events': 'none'})
                                .delay(300).animate({
                                top: -7.5,
                                left: 35,
                                height: 0
                            }, 500);

                            /* Also hide the number of 'I'm getting better !' reaction */
                            $('#module-' + (event.data.moduleId) + ' .better_nb').delay(300).hide(300);

                            /* After a delay, hide the 'Easy !' reaction image with nice animation */
                            $('#module-' + (event.data.moduleId) + ' .easy').css({'pointer-events': 'none'})
                                .delay(600).animate({
                                top: -7.5,
                                left: -10,
                                height: 0
                            }, 500);

                            /* Also hide the number of 'Easy !' reaction */
                            $('#module-' + (event.data.moduleId) + ' .easy_nb').delay(600).hide(300);

                            updateGroupImg(event.data.module, event.data.moduleId);

                            /* Show the reaction group image with nice animation */
                            $('#module-' + (event.data.moduleId) + ' .group_img').show(0).delay(500).animate({
                                top: 0,
                                left: 0,
                                height: 20
                            }, 300);

                            if (parseInt((event.data.module).getElementsByClassName('group_nb')[0].innerText) !== 0) {
                                /* Also show the number of total reaction */
                                $('#module-' + (event.data.moduleId) + ' .group_nb').delay(600).show(300);
                            }

                            $('#module-' + (event.data.moduleId) + ' .actions').delay(600).show(300);
                        }
                    }, 2000);
                }

                /**
                 * Event when the group image is mouse out
                 * @param {Object} event
                 */
                function groupImgMouseOut(event) {

                    /* Clear the animation queue to avoid image blinking */
                    $(this).stop();

                    /* Reset timerGroupImg timer */
                    clearTimeout(timerGroupImgArray[event.data.moduleId]);

                    /* IF the mouse out before the reaction group hide */
                    if ($('#module-' + (event.data.moduleId) + ' .easy').css('height') === '0px') {
                        /* Come back to the original size to inform that the image is mouse out */
                        $(this).animate({
                            top: 0,
                            left: 0,
                            height: 20
                        }, 100);
                    }
                }

                /**
                 * Event when the reaction image is mouse over
                 * @param {Object} event
                 */
                function mouseOver(event) {

                    /* Clear the animation queue to avoid image blinking */
                    $(this).stop();

                    var widthParam = $('#module-' + event.data.moduleId + ' .' + event.data.reactionName + '_txt').width();

                    /* Modification of the toolbox position (centered) */
                    $('#module-' + event.data.moduleId + ' .' + event.data.reactionName + '_txt').css({
                        'left': (event.data.leftReaction + 10) - (widthParam / 2) + 60
                    });

                    /* Get the number of 'reactionName' reaction */
                    var nbReation = parseInt((event.data.module)
                        .getElementsByClassName(event.data.reactionName + '_nb')[0].innerText);

                    /* To have the bigger image in color if there is no vote */
                    if (nbReation === 0) {
                        $('#module-' + event.data.moduleId + ' .' + event.data.reactionName)
                            .css({'-webkit-filter': '', 'filter': ''});
                    }

                    /* Pointer modification to know that we can click or interact */
                    $(this).css({'cursor': 'pointer'});

                    /* Widen a little to inform that the image is mouse over */
                    $(this).animate({
                        top: -25,
                        left: event.data.leftReaction,
                        height: 40
                    }, 100);
                }

                /**
                 * Event when the reaction image is mouse out
                 * @param {Object} event
                 */
                function mouseOut(event) {

                    /* Clear the animation queue to avoid image blinking */
                    $(this).stop();

                    /* Come back to the original size to inform that the image is mouse out */
                    $(this).animate({
                        top: -15,
                        left: event.data.leftReaction,
                        height: 20
                    }, 100);

                    /* Get the number of 'reactionName' reaction */
                    var nbReation = parseInt((event.data.module)
                        .getElementsByClassName(event.data.reactionName + '_nb')[0].innerText);

                    /* Restore the image in black and white if there is no vote */
                    if (nbReation === 0) {
                        $('#module-' + event.data.moduleId + ' .' + event.data.reactionName)
                            .css({'-webkit-filter': 'grayscale(100%)', 'filter': 'grayscale(100%)'});
                    }
                }

                /**
                 * Event when the reaction image is clicked
                 * @param {Object} event
                 */
                function onClick(event) {
                    if (userId !== null && userId !== 1) {

                        /* Get the number of 'reactionName' reaction */
                        var nbReation = parseInt((event.data.module)
                            .getElementsByClassName(event.data.reactionName + '_nb')[0].innerText);

                        /* Get the total number of reaction */
                        totalVoteArray[event.data.moduleId] = parseInt((event.data.module)
                            .getElementsByClassName('group_nb')[0].innerText);

                        /* IF there is no 'reactionName' reaction, change the emoji in black and white */
                        if (nbReation === 0) {
                            $('#module-' + event.data.moduleId + ' .' + event.data.reactionName)
                                .css({'-webkit-filter': '', 'filter': ''});
                        }

                        /* IF this is a new vote for the user */
                        if (reactionVotedArray[event.data.moduleId] === Reactions.NULL) {

                            /* AJAX call to the PHP function which add a new line in DB */
                            $.ajax({
                                type: 'POST',
                                url: '../blocks/point_view/update_db.php',
                                dataType: 'json',
                                data: {
                                    func: 'insert',
                                    userid: userId,
                                    courseid: courseId,
                                    cmid: event.data.moduleId,
                                    vote: event.data.reactionSelect
                                },
                                success: function() {
                                    /* Increment the number of the new reaction of 1 */
                                    (event.data.module).getElementsByClassName(event.data.reactionName + '_nb')[0]
                                        .innerText = (nbReation + 1);

                                    /* Update the text appearance to know that this is the selected reaction */
                                    $('#module-' + event.data.moduleId + ' .' + event.data.reactionName + '_nb').css({
                                        'font-weight': 'bold',
                                        'color': '#5585B6'
                                    });

                                    /* Update the value of total number reaction with an increment of 1 */
                                    (event.data.module).getElementsByClassName('group_nb')[0].innerText =
                                        (totalVoteArray[event.data.moduleId] + 1);

                                    /* Update the current reation with the new one */
                                    reactionVotedArray[event.data.moduleId] = event.data.reactionSelect;

                                }
                            });
                        } else if (reactionVotedArray[event.data.moduleId] === event.data.reactionSelect) {
                            /* IF the user canceled its vote */

                            /* AJAX call to the PHP function which remove a line in DB */
                            $.ajax({
                                type: 'POST',
                                url: '../blocks/point_view/update_db.php',
                                dataType: 'json',
                                data: {
                                    func: 'remove',
                                    userid: userId,
                                    courseid: courseId,
                                    cmid: event.data.moduleId,
                                    vote: event.data.reactionSelect
                                },

                                success: function() {
                                    /* Decrement the number of old of 1 */
                                    nbReation--;

                                    /* Update the number of old reaction */
                                    (event.data.module).getElementsByClassName(event.data.reactionName + '_nb')[0]
                                        .innerText = nbReation;

                                    /* Update the text appearance to know that this is no longer the selected reaction */
                                    $('#module-' + event.data.moduleId + ' .' + event.data.reactionName + '_nb').css({
                                        'font-weight': 'normal',
                                        'color': 'black'
                                    });

                                    /*
                                        * IF after the decrementation, the number of old reaction is 0
                                        * THEN change the emoji in black and white
                                        */
                                    if (nbReation === 0) {
                                        $('#module-' + event.data.moduleId + ' .' + event.data.reactionName)
                                            .css({'-webkit-filter': 'grayscale(100%)', 'filter': 'grayscale(100%)'});
                                    }

                                    /* Update the value of total number reaction with an decrement of 1 */
                                    (event.data.module).getElementsByClassName('group_nb')[0].innerText =
                                        (totalVoteArray[event.data.moduleId] - 1);

                                    /* Update the current reaction with 'none reaction' */
                                    reactionVotedArray[event.data.moduleId] = Reactions.NULL;
                                }
                            });
                        } else {
                            /* IF the user update its vote */

                            /* AJAX call to the PHP function which update a line in DB */
                            $.ajax({
                                type: 'POST',
                                url: '../blocks/point_view/update_db.php',
                                dataType: 'json',
                                data: {
                                    func: 'update',
                                    userid: userId,
                                    courseid: courseId,
                                    cmid: event.data.moduleId,
                                    vote: event.data.reactionSelect
                                },

                                success: function() {
                                    /* Increment the number of 'reactionName' reaction of 1 */
                                    (event.data.module).getElementsByClassName(event.data.reactionName + '_nb')[0]
                                        .innerText = (nbReation + 1);

                                    /* Update the text appearance to know that this is the selected reaction */
                                    $('#module-' + (event.data.moduleId) + ' .' + (event.data.reactionName) + '_nb').css({
                                        'font-weight': 'bold',
                                        'color': '#5585B6'
                                    });

                                    /* Find the name of the reaction selected */
                                    var reationSelectName;
                                    switch (reactionVotedArray[event.data.moduleId]) {
                                        case Reactions.EASY:
                                            reationSelectName = 'easy';
                                            break;
                                        case Reactions.BETTER:
                                            reationSelectName = 'better';
                                            break;
                                        case Reactions.HARD:
                                            reationSelectName = 'hard';
                                            break;
                                    }

                                    /*  Get the current number of the old reaction and decrement it of 1 */
                                    var nbReationSelect = parseInt((event.data.module)
                                        .getElementsByClassName(reationSelectName + '_nb')[0].innerText) - 1;

                                    /* Update the value of the old reaction */
                                    (event.data.module).getElementsByClassName(reationSelectName + '_nb')[0]
                                        .innerText = nbReationSelect;

                                    /* Update the text appearance to know that this is no longer the selected reaction */
                                    $('#module-' + (event.data.moduleId) + ' .' + reationSelectName + '_nb').css({
                                        'font-weight': 'normal',
                                        'color': 'black'
                                    });

                                    /*
                                    * IF after the decrementation, the number of the old
                                    * reaction is 0 THEN change the emoji in black and white
                                    */
                                    if (nbReationSelect === 0) {
                                        $('#module-' + (event.data.moduleId) + ' .' + reationSelectName)
                                            .css({'-webkit-filter': 'grayscale(100%)', 'filter': 'grayscale(100%)'});
                                    }

                                    /* Update the current reation with the new one */
                                    reactionVotedArray[event.data.moduleId] = event.data.reactionSelect;
                                }
                            });
                        }
                    }
                }

                /**
                 * Event when the reaction zone is mouse over
                 * @param {Object} event
                 */
                function reactionMouseOver(event) {

                    /* The mouse is over the reaction zone */
                    reactionArray[event.data.moduleId] = true;
                }

                /**
                 * Event when the reaction zone is mouse out
                 * @param {Object} event
                 */
                function reactionMouseOut(event) {

                    /* The mouse is out the reaction zone */
                    reactionArray[event.data.moduleId] = false;

                    /* Reset timerReactions timer */
                    clearTimeout(timerReactionsArray[event.data.moduleId]);

                    /* IF the mouse stay over at least 3 seconds... */
                    timerReactionsArray[event.data.moduleId] = setTimeout(function() {

                        /* BUT that the mouse is note in the reaction zone */
                        if (!(reactionArray[event.data.moduleId])) {

                            /*
                            * Disable the pointer events for each reactions images. This is to avoid a
                            * bug, because this is possible  select a reaction during the hiding and
                            * it create a bad comportment
                            */

                            /*
                            * After a short delay, hide the 'So Hard...' reaction image with nice
                            * animation
                            */

                            $('#module-' + (event.data.moduleId) + ' .hard').css({'pointer-events': 'none'})
                                .delay(50).animate({
                                top: -7.5,
                                left: 80,
                                height: 0
                            }, 500);

                            /* Also hide the number of 'So Hard...' reaction */
                            $('#module-' + (event.data.moduleId) + ' .hard_nb').delay(50).hide(300);

                            /*
                            * After a delay, show the 'I'm getting better !' reaction image with nice
                            * animation
                            */
                            $('#module-' + (event.data.moduleId) + ' .better').css({'pointer-events': 'none'})
                                .delay(300).animate({
                                top: -7.5,
                                left: 35,
                                height: 0
                            }, 500);

                            /* Also hide the number of 'I'm getting better !' reaction */
                            $('#module-' + (event.data.moduleId) + ' .better_nb').delay(300).hide(300);

                            /* After a delay, hide the 'Easy !' reaction image with nice animation */
                            $('#module-' + (event.data.moduleId) + ' .easy').css({'pointer-events': 'none'})
                                .delay(600).animate({
                                top: -7.5,
                                left: -10,
                                height: 0
                            }, 500);

                            /* Also hide the number of 'Easy !' reaction */
                            $('#module-' + (event.data.moduleId) + ' .easy_nb').delay(600).hide(300);

                            updateGroupImg(event.data.module, event.data.moduleId);

                            /* Show the reaction group image with nice animation */
                            $('#module-' + (event.data.moduleId) + ' .group_img').show(0).delay(500).animate({
                                top: 0,
                                left: 0,
                                height: 20
                            }, 300);

                            if (parseInt((event.data.module).getElementsByClassName('group_nb')[0].innerText) !== 0) {
                                /* Also show the number of total reaction */
                                $('#module-' + (event.data.moduleId) + ' .group_nb').delay(600).show(300);
                            }

                            $('#module-' + (event.data.moduleId) + ' .actions').delay(600).show(300);
                        }
                    }, 1000);
                }

                /* For each selected module, create a reaction zone */
                moduleSelect.forEach(function(moduleIdParam) {
                    var moduleId = parseInt(moduleIdParam);
                    if (document.getElementById('module-' + moduleId) !== null) {

                        $('#module-' + moduleIdParam)
                            .mouseover(function() {
                                $(this).css({
                                    'background': 'linear-gradient(to right, #f4f4f4, #f4f4f4, white)',
                                    'border-radius': '5px'
                                });
                            })
                            .mouseout(function() {
                                $(this).css({'background': ''});
                            });

                        var pointViewsModule = searchModule(moduleId);

                        /* Create the HTML block necessary to each activity */
                        var htmlBlock = '<div class="reactions">' +
                            '<!-- EASY ! --><span class="tooltipreaction">' +
                            '<img src="' + pix.easy + '" alt=" " class="easy"/>' +
                            '<span class="tooltiptextreaction easy_txt">' + pix.easytxt + '</span></span>' +
                            '<span class="easy_nb">' + pointViewsModule.typeone + '</span>' +
                            '<!-- I\'M GETTING BETTER --><span class="tooltipreaction">' +
                            '<img src="' + pix.better + '" alt=" " class="better"/>' +
                            '<span class="tooltiptextreaction better_txt">' + pix.bettertxt + '</span></span>' +
                            '<span class="better_nb">' + pointViewsModule.typetwo + '</span>' +
                            '<!-- SO HARD... --><span class="tooltipreaction">' +
                            '<img src="' + pix.hard + '" alt=" " class="hard"/>' +
                            '<span class="tooltiptextreaction hard_txt">' + pix.hardtxt + '</span></span>' +
                            '<span class="hard_nb">' + pointViewsModule.typethree + '</span></div>' +
                            '<!-- GROUP --><div class="group">' +
                            '<img src="" alt=" " class="group_img"/>' +
                            '<span class="group_nb">' + pointViewsModule.total + '</span></div>';

                        /* Export the HTML block */
                        $('#module-' + moduleId + ' .activityinstance').append(htmlBlock);

                        /* Initialise reactionVotedArray and CSS */
                        switch (parseInt(pointViewsModule.uservote)) {
                            case 1:
                                reactionVotedArray[moduleId] = Reactions.EASY;
                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + moduleId + ' .easy_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });
                                break;
                            case 2:
                                reactionVotedArray[moduleId] = Reactions.BETTER;
                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + moduleId + ' .better_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });
                                break;
                            case 3:
                                reactionVotedArray[moduleId] = Reactions.HARD;
                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + moduleId + ' .hard_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });
                                break;
                            default:
                                reactionVotedArray[moduleId] = Reactions.NULL;
                                break;
                        }

                        /* Shortcut to select the 'module-' + moduleId ID in the page */
                        var module = document.getElementById('module-' + moduleId);

                        if (parseInt(module.getElementsByClassName('group_nb')[0].innerText) === 0) {
                            /* Also show the number of total reaction */
                            $('#module-' + moduleId + ' .group_nb').hide();
                        }

                        updateGroupImg(module, moduleId);

                        /* Management of the reaction group */
                        $('#module-' + moduleId + ' .group_img')

                        /* MOUSE OVER */
                            .mouseover({module: module, moduleId: moduleId}, groupImgMouseOver)
                            .click({module: module, moduleId: moduleId}, groupImgMouseOver)

                            /* MOUSE OUT */
                            .mouseout({moduleId: moduleId}, groupImgMouseOut);

                        /* Management of the 'Easy !' reaction */
                        $('#module-' + moduleId + ' .easy')

                        /* MOUSE OVER */
                            .mouseover({
                                module: module, moduleId: moduleId, reactionName: 'easy',
                                className: 'easy_txt', leftReaction: -30
                            }, mouseOver)

                            /* MOUSE OUT */
                            .mouseout({
                                module: module, moduleId: moduleId, leftReaction: -20,
                                reactionName: 'easy'
                            }, mouseOut)

                            /* ON CLICK */
                            .click({
                                module: module, moduleId: moduleId, reactionName: 'easy',
                                reactionSelect: Reactions.EASY
                            }, onClick);

                        /* Management of the 'I'm getting better !' reaction */
                        $('#module-' + moduleId + ' .better')

                        /* MOUSE OVER */
                            .mouseover({
                                module: module, moduleId: moduleId, reactionName: 'better',
                                className: 'better_txt', leftReaction: 15
                            }, mouseOver)

                            /* MOUSE OUT */
                            .mouseout({
                                module: module, moduleId: moduleId, leftReaction: 25,
                                reactionName: 'better'
                            }, mouseOut)

                            /* ON CLICK */
                            .click({
                                module: module, moduleId: moduleId, reactionName: 'better',
                                reactionSelect: Reactions.BETTER
                            }, onClick);

                        var hardSelector = $('#module-' + moduleId + ' .hard');
                        var hardLeft = parseInt((hardSelector.css('left')).slice(0, -2), 10);

                        /* Management of the 'So hard...' reaction */
                        hardSelector

                        /* MOUSE OVER */
                            .mouseover({
                                module: module, moduleId: moduleId, reactionName: 'hard',
                                className: 'hard_txt', leftReaction: (hardLeft - 20)
                            }, mouseOver)

                            /* MOUSE OUT */
                            .mouseout({
                                module: module, moduleId: moduleId, leftReaction: (hardLeft - 10),
                                reactionName: 'hard'
                            }, mouseOut)

                            /* ON CLICK */
                            .click({
                                module: module, moduleId: moduleId, reactionName: 'hard',
                                reactionSelect: Reactions.HARD
                            }, onClick);


                        /* Management of the reaction zone */
                        $('#module-' + moduleId + ' .reactions')

                        /* MOUSE OVER */
                            .mouseover({moduleId: moduleId}, reactionMouseOver)

                            /* MOUSE OUT */
                            .mouseout({module: module, moduleId: moduleId}, reactionMouseOut);
                    }
                });

                /* Display difficulty tracks */
                $.each(difficultylevels, function(key, value) {
                    if (value !== '0') {
                        var difficulty;
                        switch (parseInt(value)) {
                            case 1:
                                difficulty = 'green';
                                break;
                            case 2:
                                difficulty = 'blue';
                                break;
                            case 3:
                                difficulty = 'red';
                                break;
                            case 4:
                                difficulty = 'black';
                                break;
                        }

                        var difficultyBlock = '<span class="track ' + difficulty + 'track"></span>';

                        $('#module-' + key + ' .activityinstance').prepend(difficultyBlock);
                    } else {
                        var difficultyBlockEmpty = '<span class="track"></span>';
                        $('#module-' + key + ' .activityinstance').prepend(difficultyBlockEmpty);
                    }
                });

                /* Set the colors of difficulty tracks */
                $('.greentrack').css({'background-color': envconf.greentrack});
                $('.bluetrack').css({'background-color': envconf.bluetrack});
                $('.redtrack').css({'background-color': envconf.redtrack});
                $('.blacktrack').css({'background-color': envconf.blacktrack});

                /* Add animation to menu button */
                $('#menu_point_view_img')
                    .mouseover(function() {
                        $(this).css({
                            '-webkit-filter': 'invert(100%)',
                            '-moz-filter': 'invert(100%)',
                            '-o-filter': 'invert(100%)',
                            '-ms-filter': 'invert(100%)'
                        });
                    })
                    .mouseout(function() {
                        $(this).css({
                            '-webkit-filter': 'invert(0%)',
                            '-moz-filter': 'invert(0%)',
                            '-o-filter': 'invert(0%)',
                            '-ms-filter': 'invert(0%)'
                        });
                    });
            });
        }
    };
});