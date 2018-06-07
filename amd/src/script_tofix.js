/* Include JQuery */
define(['jquery'], function ($) {
    return {
        init: function (likes, userid) {

            /* Wait that the DOM is fully loaded. */
            $(function () {

                    var likesSQL = likes;
                    var userId = userid;

                    var likesModule;

                    /* Timer to see how long the mouse stay over the reaction group image */
                    var timerGroupImg;
                    /* Timer to see how long the mouse stay out of the reaction zone */
                    var timerReactions;

                    /* Enumeration of the possible reactions */
                    var Reactions = {
                        NULL: 0,
                        EASY: 1,
                        BETTER: 2,
                        HARD: 3
                    };

                    /* Boolean which determine if the mouse is over or not the reaction zone */
                    var reaction = false;

                    /*
                        * [TODO] Upgrade Comment
                        * Total number of reaction for the activity, here module-19. It need to be read
                        * in the database
                        */

                    var totalVote;

                    /**
                     * Function which modify the reaction group image in terms of kind of vote
                     */
                    function updateGroupImg(module, moduleId) {

                        /* eslint-disable no-console */
                        console.log(module);
                        /* eslint-enable no-console */

                        /* Get the number of reaction for each one of it */
                        var easyVote = parseInt(module.getElementsByClassName('easy_nb')[0].innerText);
                        var betterVote = parseInt(module.getElementsByClassName('better_nb')[0].innerText);
                        var hardVote = parseInt(module.getElementsByClassName('hard_nb')[0].innerText);
                        var groupImg = '';

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

                        /* Modify the image source of the reaction group */
                        $('#module-' + moduleId + ' .group_img').attr('src', '../blocks/like/img/group_' + groupImg + '.png');
                    }

                    /**
                     * [TODO]
                     * @param moduleId
                     */
                    function searchModule(moduleId) {
                        var assign = false;
                        var resultSearch;

                        likesSQL.forEach(function (element) {
                            if (parseInt(element.cmid) === moduleId) {
                                resultSearch = element;
                                assign = true;
                            }
                        });

                        if (!assign) {
                            resultSearch = {
                                'cmid': moduleId.toString(),
                                'type': '1',
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
                     *
                     * @param event
                     */
                    function groupImgMouseOver(event) {

                        /* eslint-disable no-console */
                        console.log('MouseOver');
                        /* eslint-enable no-console */

                        /* Pointer modification to inform a possible click or interaction */
                        $(this).css({'cursor': 'pointer'});

                        /* Widen a little to inform that the image is mouse over */
                        $(this).animate({
                            top: -1.5,
                            left: -3,
                            height: 23
                        }, 100);

                        /* Clear the animation queue to avoid image blinking */
                        $(this).clearQueue();

                        /* IF the mouse stay over at least 0.3 seconds */
                        timerGroupImg = setTimeout(function () {

                            /* Reactions images modifications to black and white if no reaction has been made */
                            if (parseInt((event.data.module).getElementsByClassName('easy_nb')[0].innerText) === 0) {
                                $('#module-' + (event.data.moduleId) + ' .easy').attr('src', '../blocks/like/img/easy_BW.png');
                            }
                            if (parseInt((event.data.module).getElementsByClassName('better_nb')[0].innerText) === 0) {
                                $('#module-' + (event.data.moduleId) + ' .better').attr('src', '../blocks/like/img/better_BW.png');
                            }
                            if (parseInt((event.data.module).getElementsByClassName('hard_nb')[0].innerText) === 0) {
                                $('#module-' + (event.data.moduleId) + ' .hard').attr('src', '../blocks/like/img/hard_BW.png');
                            }

                            /*
                            * Hide the reaction group image with nice animation
                            * Completely hide the reaction group image to be sure
                            */
                            $('#module-' + (event.data.moduleId) + ' .group_img').animate({
                                top: 13,
                                left: 20,
                                height: 0
                            }, 300).hide(0);

                            /* Also hide the number of total reaction */
                            $('#module-' + (event.data.moduleId) + ' .group_nb').delay(50).hide(300);

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
                        clearTimeout(timerReactions);

                        /* IF the mouse stay over at least 3 seconds... */
                        timerReactions = setTimeout(function () {

                            /* eslint-disable no-console */
                            console.log('Reaction : ' + reaction);
                            /* eslint-enable no-console */

                            /* BUT the mouse is not in the reaction zone */
                            if (!reaction) {

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

                                /* Also show the number of total reaction */
                                $('#module-' + (event.data.moduleId) + ' .group_nb').delay(600).show(300);
                            }
                        }, 3000);
                    }

                    /**
                     *
                     * @param event
                     */
                    function groupImgMouseOut(event) {

                        /* eslint-disable no-console */
                        console.log('MouseOut');
                        /* eslint-enable no-console */

                        /* Reset timerGroupImg timer */
                        clearTimeout(timerGroupImg);

                        /* IF the mouse out before the reaction group hide */
                        if ($('#module-' + (event.data.moduleId) + ' .easy').css('height') === '0px') {
                            /* Come back to the original size to inform that the image is mouse out */
                            $(this).animate({
                                top: 0,
                                left: 0,
                                height: 20
                            }, 100);

                            /* Clear the animation queue to avoid image blinking */
                            $(this).clearQueue();
                        }
                    }

                    /**
                     * [TODO]
                     * @param module
                     * @param moduleId
                     */
                    function easyMouseOver(event) {

                        /* Length of the text inside the toolbox to have a correct size */
                        var easyTxt = (event.data.module).getElementsByClassName('easy_txt')[0].innerText;
                        var easyTxtLength = easyTxt.length;

                        /* Modification of the toolbox width */
                        $('#module-' + (event.data.moduleId) + ' .tooltip .tooltiptext').css({
                            'width': easyTxtLength * 7 + 'px',
                            'left': '15px'
                        });

                        /* Pointer modification to know that we can click or interact */
                        $(this).css({'cursor': 'pointer'});

                        /* Widen a little to inform that the image is mouse over */
                        $(this).animate({
                            top: -25,
                            left: -30,
                            height: 40
                        }, 100);

                        /* Clear the animation queue to avoid image blinking */
                        $(this).clearQueue();
                    }

                    /**
                     * [TODO]
                     */
                    function easyMouseOut() {
                        /* Come back to the original size to inform that the image is mouse out */
                        $(this).animate({
                            top: -15,
                            left: -20,
                            height: 20
                        }, 100);

                        /* Clear the animation queue to avoid image blinking */
                        $(this).clearQueue();
                    }

                    function easyClick(event) {

                        /* Get the number of 'Easy !' reaction */
                        var easyNb = parseInt((event.data.module).getElementsByClassName('easy_nb')[0].innerText);

                        /* Get the total number of reaction */
                        totalVote = parseInt((event.data.module).getElementsByClassName('group_nb')[0].innerText);

                        /* IF there is no 'Easy !' reaction, change the emoji in black and white */
                        if (easyNb === 0) {
                            $('#module-' + (event.data.moduleId) + ' .easy').attr('src', '../blocks/like/img/easy.png');
                        }

                        /* It depends now of the previous reaction vote */
                        switch ((event.data.reactionVoted)) {
                            case Reactions.EASY:

                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'remove',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.EASY
                                    },

                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Decrement the number of 'Easy !' of 1 */
                                easyNb = easyNb - 1;

                                /* Update the number of 'Easy !' reaction */
                                (event.data.module).getElementsByClassName('easy_nb')[0].innerText = easyNb;

                                /* Update the text appearance to know that this is no longer the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .easy_nb').css({
                                    'font-weight': 'normal',
                                    'color': 'black'
                                });

                                /*
                                * IF after the decrementation, the number of 'Easy !' reaction is 0
                                * THEN change the emoji in black and white
                                */
                                if (easyNb === 0) {
                                    $('#module-' + (event.data.moduleId) + ' .easy').attr('src', '../blocks/like/img/easy_BW.png');
                                }

                                /* Update the value of total number reaction with an decrement of 1 */
                                (event.data.module).getElementsByClassName('group_nb')[0].innerText = (totalVote - 1);

                                /* Update the current reaction with 'none reaction' */
                                (event.data.reactionVoted) = Reactions.NULL;
                                break;

                            /* IF the previous was 'I'm getting better !'*/
                            case Reactions.BETTER:

                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'update',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.EASY
                                    },

                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Increment the number of 'Easy !' reaction of 1 */
                                (event.data.module).getElementsByClassName('easy_nb')[0].innerText = (easyNb + 1);

                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .easy_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });

                                /*  Get the current number of 'I'm getting better !' reaction and decrement it of 1 */
                                var betterNb = parseInt((event.data.module).getElementsByClassName('better_nb')[0].innerText) - 1;

                                /* Update the value of 'I'm getting better !' reaction */
                                (event.data.module).getElementsByClassName('better_nb')[0].innerText = betterNb;

                                /* Update the text appearance to know that this is no longer the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .better_nb').css({
                                    'font-weight': 'normal',
                                    'color': 'black'
                                });

                                /*
                                * IF after the decrementation, the number of 'I'm getting better !'
                                * reaction is 0 THEN change the emoji in black and white
                                */
                                if (betterNb === 0) {
                                    $('#module-' + (event.data.moduleId) + ' .better')
                                        .attr('src', '../blocks/like/img/better_BW.png');
                                }

                                /* Update the current reation with 'Easy !' */
                                (event.data.reactionVoted) = Reactions.EASY;
                                break;

                            /* IF the previous was 'So hard...'*/
                            case Reactions.HARD:

                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'update',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.EASY
                                    },

                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Increment the number of 'Easy !' reaction of 1 */
                                (event.data.module).getElementsByClassName('easy_nb')[0].innerText = (easyNb + 1);

                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .easy_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });

                                /*  Get the current number of 'So hard...' reaction and decrement it of 1 */
                                var hardNb = parseInt((event.data.module).getElementsByClassName('hard_nb')[0].innerText) - 1;

                                /* Update the value of 'So hard...' reaction */
                                (event.data.module).getElementsByClassName('hard_nb')[0].innerText = hardNb;

                                /* Update the text appearance to know that this is no longer the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .hard_nb').css({
                                    'font-weight': 'normal',
                                    'color': 'black'
                                });

                                /*
                                * IF after the decrementation, the number of 'So hard...' reaction is 0
                                * THEN change the emoji in black and white
                                */
                                if (hardNb === 0) {
                                    $('#module-' + (event.data.moduleId) + ' .hard').attr('src', '../blocks/like/img/hard_BW.png');
                                }

                                /* Update the current reation with 'Easy !' */
                                (event.data.reactionVoted) = Reactions.EASY;
                                break;

                            /* IF there is no previous vote */
                            case Reactions.NULL:

                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'insert',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.EASY
                                    },
                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Increment the number of 'Easy !' reaction of 1 */
                                (event.data.module).getElementsByClassName('easy_nb')[0].innerText = (easyNb + 1);

                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .easy_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });

                                /* Update the value of total number reaction with an increment of 1 */
                                (event.data.module).getElementsByClassName('group_nb')[0].innerText = (totalVote + 1);

                                /* Update the current reation with 'Easy !' */
                                (event.data.reactionVoted) = Reactions.EASY;
                                break;
                        }
                    }

                    function betterMouseOver(event) {

                        /* Length of the text inside the toolbox to have a correct size */
                        var betterTxt = document.getElementById('module-' + (event.data.moduleId))
                            .getElementsByClassName('better_txt')[0].innerText;
                        var betterTxtLength = betterTxt.length;

                        /* Modification of the toolbox width */
                        $('#module-' + (event.data.moduleId) + ' .tooltip .tooltiptext').css({
                            'width': betterTxtLength * 7 + 'px',
                            'left': '40px'
                        });

                        /* Pointer modification to know that we can click or interact */
                        $(this).css({'cursor': 'pointer'});

                        /* Widen a little to inform that the image is mouse over */
                        $(this).animate({
                            top: -25,
                            left: 15,
                            height: 40
                        }, 100);

                        /* Clear the animation queue to avoid image blinking */
                        $(this).clearQueue();
                    }

                    function betterMouseOut() {

                        /* Come back to the original size to inform that the image is mouse out */
                        $(this).animate({
                            top: -15,
                            left: 25,
                            height: 20
                        }, 100);

                        /* Clear the animation queue to avoid image blinking */
                        $(this).clearQueue();
                    }

                    function betterClick(event) {

                        /* Get the number of 'I'm getting better' reaction */
                        var betterNb = parseInt((event.data.module).getElementsByClassName('better_nb')[0].innerText);

                        /* Get the total number of reaction */
                        totalVote = parseInt((event.data.module).getElementsByClassName('group_nb')[0].innerText);

                        /* IF there is no 'I'm getting better' reaction, change the emoji in black and white */
                        if (betterNb === 0) {
                            $('#module-' + (event.data.moduleId) + ' .better').attr('src', '../blocks/like/img/better.png');
                        }

                        /* It depends now of the previous reaction vote */
                        switch ((event.data.reactionVoted)) {

                            case Reactions.BETTER:
                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'remove',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.BETTER
                                    },
                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Decrement the number of 'I'm getting better !' of 1 */
                                betterNb = betterNb - 1;

                                /* Update the number of 'I'm getting better !' reaction */
                                (event.data.module).getElementsByClassName('better_nb')[0].innerText = betterNb;

                                /* Update the text appearance to know that this is no longer the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .better_nb').css({
                                    'font-weight': 'normal',
                                    'color': 'black'
                                });

                                /*
                                * IF after the decrementation, the number of 'I'm getting better !' reaction
                                * is 0 THEN change the emoji in black and white
                                */
                                if (betterNb === 0) {
                                    $('#module-' + (event.data.moduleId) + ' .better')
                                        .attr('src', '../blocks/like/img/better_BW.png');
                                }

                                /* Update the value of total number reaction with an decrement of 1 */
                                (event.data.module).getElementsByClassName('group_nb')[0].innerText = (totalVote - 1);

                                /* Update the current reaction with 'none reaction' */
                                (event.data.reactionVoted) = Reactions.NULL;

                                break;

                            /* IF the previous was 'Easy !'*/
                            case Reactions.EASY:

                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'update',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.BETTER
                                    },
                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Increment the number of 'I'm getting better !' reaction of 1 */
                                (event.data.module).getElementsByClassName('better_nb')[0].innerText = (betterNb + 1);

                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .better_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });

                                /*  Get the current number of 'Easy !' reaction and decrement it of 1 */
                                var easyNb = parseInt((event.data.module).getElementsByClassName('easy_nb')[0].innerText) - 1;

                                /* Update the value of 'Easy !' reaction */
                                (event.data.module).getElementsByClassName('easy_nb')[0].innerText = easyNb;

                                /* Update the text appearance to know that this is no longer the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .easy_nb').css({
                                    'font-weight': 'normal',
                                    'color': 'black'
                                });

                                /*
                                * IF after the decrementation, the number of 'Easy !' reaction is 0
                                * THEN change the emoji in black and white
                                */
                                if (easyNb === 0) {
                                    $('#module-' + (event.data.moduleId) + ' .easy').attr('src', '../blocks/like/img/easy_BW.png');
                                }

                                /* Update the current reation with 'I'm getting better !' */
                                (event.data.reactionVoted) = Reactions.BETTER;
                                break;

                            /* IF the previous was 'So hard...'*/
                            case Reactions.HARD:

                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'update',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.BETTER
                                    },
                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Increment the number of 'I'm getting better !' reaction of 1 */
                                (event.data.module).getElementsByClassName('better_nb')[0].innerText = (betterNb + 1);

                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .better_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });

                                /*  Get the current number of 'So hard...' reaction and decrement it of 1 */
                                var hardNb = parseInt((event.data.module).getElementsByClassName('hard_nb')[0].innerText) - 1;

                                /* Update the value of 'So hard...' reaction */
                                (event.data.module).getElementsByClassName('hard_nb')[0].innerText = hardNb;

                                /* Update the text appearance to know that this is no longer the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .hard_nb').css({
                                    'font-weight': 'normal',
                                    'color': 'black'
                                });

                                /*
                                * IF after the decrementation, the number of 'So hard...' reaction is 0
                                * THEN change the emoji in black and white
                                */
                                if (hardNb === 0) {
                                    $('#module-' + (event.data.moduleId) + ' .hard').attr('src', '../blocks/like/img/hard_BW.png');
                                }

                                /* Update the current reation with 'I'm getting better !' */
                                (event.data.reactionVoted) = Reactions.BETTER;

                                break;

                            /* IF there is no previous vote */
                            case Reactions.NULL:

                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'insert',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.BETTER
                                    },
                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Increment the number of 'I'm getting better !' reaction of 1 */
                                (event.data.module).getElementsByClassName('better_nb')[0].innerText = (betterNb + 1);

                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .better_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });

                                /* Update the value of total number reaction with an increment of 1 */
                                (event.data.module).getElementsByClassName('group_nb')[0].innerText = (totalVote + 1);

                                /* Update the current reation with 'I'm getting better !' */
                                (event.data.reactionVoted) = Reactions.BETTER;

                                break;
                        }
                    }

                    function hardMouseOver(event) {

                        /* Length of the text inside the toolbox to have a correct size */
                        var hardTxt = (event.data.module).getElementsByClassName('hard_txt')[0].innerText;
                        var hardTxtLength = hardTxt.length;

                        /* Modification of the toolbox width */
                        $('#module-' + (event.data.moduleId) + ' .tooltip .tooltiptext').css({
                            'width': hardTxtLength * 7 + 'px',
                            'left': '105px'
                        });

                        /* Pointer modification to know that we can click or interact */
                        $(this).css({'cursor': 'pointer'});

                        /* Widen a little to inform that the image is mouse over */
                        $(this).animate({
                            top: -25,
                            left: 60,
                            height: 40
                        }, 100);

                        /* Clear the animation queue to avoid image blinking */
                        $(this).clearQueue();
                    }

                    function hardMouseOut() {

                        /* Come back to the original size to inform that the image is mouse out */
                        $(this).animate({
                            top: -15,
                            left: 70,
                            height: 20
                        }, 100);

                        /* Clear the animation queue to avoid image blinking */
                        $(this).clearQueue();
                    }

                    function hardClick(event) {

                        /* Get the number of 'So hard...' reaction */
                        var hardNb = parseInt((event.data.module).getElementsByClassName('hard_nb')[0].innerText);

                        /* Get the total number of reaction */
                        totalVote = parseInt((event.data.module).getElementsByClassName('group_nb')[0].innerText);

                        /* IF there is no 'So hard... reaction, change the emoji in black and white */
                        if (hardNb === 0) {
                            $('#module-' + (event.data.moduleId) + ' .hard').attr('src', '../blocks/like/img/hard.png');
                        }

                        /* It depends now of the previous reaction vote */
                        switch ((event.data.reactionVoted)) {

                            case Reactions.HARD:
                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'remove',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.HARD
                                    },

                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Decrement the number of So hard...' of 1 */
                                hardNb = hardNb - 1;

                                /* Update the number of 'So hard...' reaction */
                                (event.data.module).getElementsByClassName('hard_nb')[0].innerText = hardNb;

                                /* Update the text appearance to know that this is no longer the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .hard_nb').css({
                                    'font-weight': 'normal',
                                    'color': 'black'
                                });

                                /*
                                * IF after the decrementation, the number of 'So hard...' reaction is 0
                                * THEN change the emoji in black and white
                                */
                                if (hardNb === 0) {
                                    $('#module-' + (event.data.moduleId) + ' .hard').attr('src', '../blocks/like/img/hard_BW.png');
                                }

                                /* Update the value of total number reaction with an decrement of 1 */
                                (event.data.module).getElementsByClassName('group_nb')[0].innerText = (totalVote - 1);

                                /* Update the current reaction with 'none reaction' */
                                (event.data.reactionVoted) = Reactions.NULL;

                                break;

                            /* IF the previous was 'Easy !'*/
                            case Reactions.EASY:

                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'update',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.HARD
                                    },

                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Increment the number of 'So hard...' reaction of 1 */
                                (event.data.module).getElementsByClassName('hard_nb')[0].innerText = (hardNb + 1);

                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .hard_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });

                                /*  Get the current number of 'Easy !' reaction and decrement it of 1 */
                                var easyNb = parseInt((event.data.module).getElementsByClassName('easy_nb')[0].innerText) - 1;

                                /* Update the value of 'Easy !' reaction */
                                (event.data.module).getElementsByClassName('easy_nb')[0].innerText = easyNb;

                                /* Update the text appearance to know that this is no longer the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .easy_nb').css({
                                    'font-weight': 'normal',
                                    'color': 'black'
                                });

                                /*
                                * IF after the decrementation, the number of 'Easy !' reaction is 0
                                * THEN change the emoji in black and white
                                */
                                if (easyNb === 0) {
                                    $('#module-' + (event.data.moduleId) + ' .easy').attr('src', '../blocks/like/img/easy_BW.png');
                                }

                                /* Update the current reation with 'So hard...' */
                                (event.data.reactionVoted) = Reactions.HARD;
                                break;

                            /* IF the previous was 'I'm getting better !'*/
                            case Reactions.BETTER:

                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'update',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.HARD
                                    },

                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Increment the number of 'So hard...' reaction of 1 */
                                (event.data.module).getElementsByClassName('hard_nb')[0].innerText = (hardNb + 1);

                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .hard_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });

                                /*  Get the current number of 'I'm getting better !' reaction and decrement it of 1 */
                                var betterNb = parseInt((event.data.module).getElementsByClassName('better_nb')[0].innerText) - 1;

                                /* Update the value of 'I'm getting better !' reaction */
                                (event.data.module).getElementsByClassName('better_nb')[0].innerText = betterNb;

                                /* Update the text appearance to know that this is no longer the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .better_nb').css({
                                    'font-weight': 'normal',
                                    'color': 'black'
                                });

                                /*
                                * IF after the decrementation, the number of 'I'm getting better !'
                                * reaction is 0 THEN change the emoji in black and white
                                */
                                if (betterNb === 0) {
                                    $('#module-' + (event.data.moduleId) + ' .better')
                                        .attr('src', '../blocks/like/img/better_BW.png');
                                }

                                /* Update the current reation with 'So hard...' */
                                (event.data.reactionVoted) = Reactions.HARD;
                                break;

                            /* IF there is no previous vote */
                            case Reactions.NULL:

                                /* [TODO] Comment */
                                $.ajax({
                                    type: 'POST',
                                    url: '../blocks/like/update_db.php',
                                    dataType: 'json',
                                    data: {
                                        func: 'insert',
                                        userid: userId,
                                        cmid: (event.data.moduleId),
                                        type: 1,
                                        vote: Reactions.HARD
                                    },

                                    success: function (output) {
                                        /* eslint-disable no-console */
                                        console.log(output);
                                        /* eslint-enable no-console */
                                    }
                                });

                                /* Increment the number of 'So hard...' reaction of 1 */
                                (event.data.module).getElementsByClassName('hard_nb')[0].innerText = (hardNb + 1);

                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + (event.data.moduleId) + ' .hard_nb').css({
                                    'font-weight': 'bold',
                                    'color': '#5585B6'
                                });

                                /* Update the value of total number reaction with an increment of 1 */
                                (event.data.module).getElementsByClassName('group_nb')[0].innerText = (totalVote + 1);

                                /* Update the current reation with 'So hard...' */
                                (event.data.reactionVoted) = Reactions.HARD;
                                break;
                        }
                    }

                    function reactionMouseOver() {

                        /* The mouse is over the reaction zone */
                        reaction = true;
                    }

                    function reactionMouseOut(event) {

                        /* The mouse is out the reaction zone */
                        reaction = false;

                        /* Reset timerReactions timer */
                        clearTimeout(timerReactions);

                        /* IF the mouse stay over at least 3 seconds... */
                        timerReactions = setTimeout(function () {

                            /* BUT that the mouse is note in the reaction zone */
                            if (!reaction) {

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

                                /* Also show the number of total reaction */
                                $('#module-' + (event.data.moduleId) + ' .group_nb').delay(600).show(300);
                            }
                        }, 1000);
                    }


                    for (var moduleId = 19; moduleId <= 22; moduleId++) {

                        likesModule = searchModule(moduleId);

                        /* eslint-disable no-console */
                        console.log(moduleId + ' : ' + JSON.stringify(likesModule));
                        /* eslint-enable no-console */

                        /* Create the HTML block necessary to each activity */
                        var htmlBlock = '<div class="reactions">' +
                            '<!-- EASY ! --><span class="tooltip">' +
                            '<img src="../blocks/like/img/easy.png" alt=" " class="easy"/>' +
                            '<span class="tooltiptext easy_txt">Fastoche !</span></span>' +
                            '<span class="easy_nb">' + likesModule.typeone + '</span>' +
                            '<!-- I\'M GETTING BETTER --><span class="tooltip">' +
                            '<img src="../blocks/like/img/better.png" alt=" " class="better"/>' +
                            '<span class="tooltiptext better_txt">Je m\'améliore !</span></span>' +
                            '<span class="better_nb">' + likesModule.typetwo + '</span>' +
                            '<!-- SO HARD... --><span class="tooltip">' +
                            '<img src="../blocks/like/img/hard.png" alt=" " class="hard"/>' +
                            '<span class="tooltiptext hard_txt">Dur dur...</span></span>' +
                            '<span class="hard_nb">' + likesModule.typethree + '</span></div>' +
                            '<!-- GROUP --><div class="group">' +
                            '<img src="" alt=" " class="group_img"/>' +
                            '<span class="group_nb">' + likesModule.total + '</span></div>';

                        /* Export the HTML block */
                        $('#module-' + moduleId + ' .activityinstance').append(htmlBlock);

                        /* eslint-disable no-console */
                        console.log(moduleId + ' : Code appended');
                        /* eslint-enable no-console */

                        /*
                        * [TODO] Upgrade comment
                        * Reaction of the user for the activity, here the module-19, NULL by default. But
                        * it need to be read in the database
                        */
                        var reactionVoted;
                        switch (parseInt(likesModule.uservote)) {
                            case 1:
                                reactionVoted = Reactions.EASY;
                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + moduleId + ' .easy_nb').css({'font-weight': 'bold', 'color': '#5585B6'});
                                break;
                            case 2:
                                reactionVoted = Reactions.BETTER;
                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + moduleId + ' .better_nb').css({'font-weight': 'bold', 'color': '#5585B6'});
                                break;
                            case 3:
                                reactionVoted = Reactions.HARD;
                                /* Update the text appearance to know that this is the selected reaction */
                                $('#module-' + moduleId + ' .hard_nb').css({'font-weight': 'bold', 'color': '#5585B6'});
                                break;
                            default:
                                reactionVoted = Reactions.NULL;
                                break;
                        }

                        /* Shortcut to select the 'module-' + moduleId ID in the page */
                        var module = document.getElementById('module-' + moduleId);

                        updateGroupImg(module, moduleId);

                        /* Management of the reaction group */
                        $('#module-' + moduleId + ' .group_img')

                        /* MOUSE OVER */
                            .mouseover({module: module, moduleId: moduleId}, groupImgMouseOver)

                            /* MOUSE OUT */
                            .mouseout({moduleId: moduleId}, groupImgMouseOut);

                        /* Management of the 'Easy !' reaction */
                        $('#module-' + moduleId + ' .easy')

                        /* MOUSE OVER */
                            .mouseover({module: module, moduleId: moduleId}, easyMouseOver)

                            /* MOUSE OUT */
                            .mouseout(easyMouseOut)

                            /* ON CLICK */
                            .click({module: module, moduleId: moduleId, reactionVoted: reactionVoted}, easyClick);

                        /* Management of the 'I'm getting better !' reaction */
                        $('#module-' + moduleId + ' .better')

                        /* MOUSE OVER */
                            .mouseover({module: module, moduleId: moduleId}, betterMouseOver)

                            /* MOUSE OUT */
                            .mouseout(betterMouseOut)

                            /* ON CLICK */
                            .click({module: module, moduleId: moduleId, reactionVoted: reactionVoted}, betterClick);

                        /* Management of the 'So hard...' reaction */
                        $('#module-' + moduleId + ' .hard')

                        /* MOUSE OVER */
                            .mouseover({module: module, moduleId: moduleId}, hardMouseOver)

                            /* MOUSE OUT */
                            .mouseout(hardMouseOut)

                            /* ON CLICK */
                            .click({module: module, moduleId: moduleId, reactionVoted: reactionVoted}, hardClick);


                        /* Management of the reaction zone */
                        $('#module-' + moduleId + ' .reactions')

                        /* MOUSE OVER */
                            .mouseover(reactionMouseOver)

                            /* MOUSE OUT */
                            .mouseout({module: module, moduleId: moduleId}, reactionMouseOut);
                    }
                }
            );

        }
    };
});