/* Injection CSS ? Pas mon propre fichier ? */

/* Include JQuery */
define(["jquery"], function ($) {
    return {
        init: function (likes, userid) {

            /* [OLD]
            window.addEventListener("load", function(event) {
            */

            /* Wait that the DOM is fully loaded. */
            $(function () {
                    var likesSQL = likes;
                    var userId = userid;

                    /* [OLD]
                    * Import result from PHP
                    * $.ajax({
                    *     method: 'POST',
                    *     url: '../blocks/like/database.php',
                    *     data: {},
                    *     success: function(output) {
                    *         console.log('Data : ' + output);
                    *     },
                    *     dataType: 'json'
                    * });
                    */

                    /* Create the HTML block necessary to each activity */
                    var htmlBlock = '<div class="reactions">' +
                        '<!-- EASY ! --><span class="tooltip">' +
                        '<img src="../blocks/like/img//easy.png" alt=" " ' + 'class="easy"/>' +
                        '<span class="tooltiptext easy_txt">Fastoche !</span></span>' +
                        '<span class="easy_nb">' + likesSQL.typeone + '</span>' +
                        '<!-- I\'M GETTING BETTER --><span class="tooltip">' +
                        '<img src="../blocks/like/img//better.png" alt=" " class="better"/>' +
                        '<span class="tooltiptext better_txt">Je m\'améliore !</span></span>' +
                        '<span class="better_nb">' + likesSQL.typetwo + '</span>' +
                        '<!-- SO HARD... --><span class="tooltip">' +
                        '<img src="../blocks/like/img//hard.png" alt=" " class="hard"/>' +
                        '<span class="tooltiptext hard_txt">Dur dur...</span></span>' +
                        '<span class="hard_nb">' + likesSQL.typethree + '</span></div>' +
                        '<!-- GROUP --><div class="group">' +
                        '<img src="" alt=" " class="group_img"/>' +
                        '<span class="group_nb">' + likesSQL.total + '</span></div>';

                    /* Export the HTML block */
                    $("#module-19 .activityinstance").append(htmlBlock);

                    /* Timer to see how long the mouse stay over the reaction group image */
                    var timerGroupImgM19;
                    /* Timer to see how long the mouse stay out of the reaction zone */
                    var timerReactionsM19;
                    /* Boolean which determine if the mouse is over or not the reaction zone */
                    var reactionM19 = false;

                    /* Enumeration of the possible reactions */
                    var Reactions = {
                        NULL: 0,
                        EASY: 1,
                        BETTER: 2,
                        HARD: 3
                    };

                    /*
                    * [TODO] Upgrade comment
                    * Reaction of the user for the activity, here the module-19, NULL by default. But
                    * it need to be read in the database
                    */
                    var reactionVotedM19;
                    switch (parseInt(likesSQL.uservote)) {
                        case 0:
                            reactionVotedM19 = Reactions.NULL;
                            break;
                        case 1:
                            reactionVotedM19 = Reactions.EASY;
                            /* Update the text appearance to know that this is the selected reaction */
                            $("#module-19 .easy_nb").css({"font-weight": "bold", "color": "#5585B6"});
                            break;
                        case 2:
                            reactionVotedM19 = Reactions.BETTER;
                            /* Update the text appearance to know that this is the selected reaction */
                            $("#module-19 .better_nb").css({"font-weight": "bold", "color": "#5585B6"});
                            break;
                        case 3:
                            reactionVotedM19 = Reactions.HARD;
                            /* Update the text appearance to know that this is the selected reaction */
                            $("#module-19 .hard_nb").css({"font-weight": "bold", "color": "#5585B6"});
                            break;
                    }

                    /*
                    * Total number of reaction for the activity, here module-19. It need to be read
                    * in the database
                    */
                    var totalVoteM19;

                    /* Shortcut to select the "module-19" ID in the page */
                    var module19 = document.getElementById("module-19");

                    /**
                     * Function which modify the reaction group image in terms of kind of vote
                     */
                    function updateGroupImg() {

                        /* Get the number of reaction for each one of it */
                        var easyVoteM19 = parseInt(module19.getElementsByClassName("easy_nb")[0].innerText);
                        var betterVoteM19 = parseInt(module19.getElementsByClassName("better_nb")[0].innerText);
                        var hardVoteM19 = parseInt(module19.getElementsByClassName("hard_nb")[0].innerText);
                        var groupImgM19 = "";

                        /* Add the image suffix if there is at least 1 vote for the selected reaction */
                        if (easyVoteM19) {
                            groupImgM19 += "E";
                        }
                        if (betterVoteM19) {
                            groupImgM19 += "B";
                        }
                        if (hardVoteM19) {
                            groupImgM19 += "H";
                        }

                        /* Modify the image source of the reaction group */
                        $("#module-19 .group_img").attr("src", "../blocks/like/img/group_" + groupImgM19 + ".png");
                    }

                    updateGroupImg();

                    /* Management of the reaction group */
                    $("#module-19 .group_img")

                    /* MOUSE OVER */
                        .mouseover(function GroupImgMouseOver() {

                            /* Pointer modification to inform a possible click or interaction */
                            $(this).css({"cursor": "pointer"});

                            /* Widen a little to inform that the image is mouse over */
                            $(this).animate({
                                top: -1.5,
                                left: -3,
                                height: 23
                            }, 100);

                            /* Clear the animation queue to avoid image blinking */
                            $(this).clearQueue();

                            var snapShotGroupImg = this;

                            /* IF the mouse stay over at least 0.3 seconds */
                            timerGroupImgM19 = setTimeout(function () {

                                /* Reactions images modifications to black and white if no reaction has been made */
                                if (parseInt(module19.getElementsByClassName("easy_nb")[0].innerText) === 0) {
                                    $("#module-19 .easy").attr("src", "../blocks/like/img//easy_BW.png");
                                }
                                if (parseInt(module19.getElementsByClassName("better_nb")[0].innerText) === 0) {
                                    $("#module-19 .better").attr("src", "../blocks/like/img//better_BW.png");
                                }
                                if (parseInt(module19.getElementsByClassName("hard_nb")[0].innerText) === 0) {
                                    $("#module-19 .hard").attr("src", "../blocks/like/img//hard_BW.png");
                                }

                                /* Hide the reaction group image with nice animation */
                                $(snapShotGroupImg).animate({
                                    top: 13,
                                    left: 20,
                                    height: 0
                                }, 300);

                                /* Completely hide the reaction group image to be sure */
                                $(snapShotGroupImg).hide(0);

                                /* Also hide the number of total reaction */
                                $("#module-19 .group_nb").delay(50).hide(300);

                                /* Enable the pointer events for each reactions images */

                                /* After a short delay, show the "Easy !" reaction image with nice animation */
                                $("#module-19 .easy").delay(50).animate({
                                    top: -15,
                                    left: -20,
                                    height: 20
                                }, 300)
                                    .css({"pointer-events": "auto"});

                                /* Also show the number of "Easy !" reaction */
                                $("#module-19 .easy_nb").delay(50).show(300);

                                /*
                                * After a delay, show the "I'm getting better !" reaction image with nice
                                * animation
                                */
                                $("#module-19 .better").delay(200).animate({
                                    top: -15,
                                    left: 25,
                                    height: 20
                                }, 300)
                                    .css({"pointer-events": "auto"});

                                /* Also show the number of "I'm getting better !" reaction */
                                $("#module-19 .better_nb").delay(200).show(300);

                                /* After a delay, show the "So Hard..." reaction image with nice animation */
                                $("#module-19 .hard").delay(400).animate({
                                    top: -15,
                                    left: 70,
                                    height: 20
                                }, 300)
                                    .css({"pointer-events": "auto"});

                                /* Also show the number of "So Hard..." reaction */
                                $("#module-19 .hard_nb").delay(400).show(300);
                            }, 500);

                            /* Snapshot the current state */
                            var snapShotGroupImg2 = this;

                            /* Reset timerReactions timer */
                            clearTimeout(timerReactionsM19);

                            /* IF the mouse stay over at least 3 seconds... */
                            timerReactionsM19 = setTimeout(function () {

                                /* BUT the mouse is not in the reaction zone */
                                if (!reactionM19) {

                                    /*
                                    * Disable the pointer events for each reactions images. This is to avoid a
                                    * bug, because this is possible  select a reaction during the hiding and
                                    * it create a bad comportment
                                    */

                                    /*
                                    * After a short delay, hide the "So Hard..." reaction image with nice
                                    * animation
                                    */
                                    $("#module-19 .hard").css({"pointer-events": "none"})
                                        .delay(50).animate({
                                        top: -7.5,
                                        left: 80,
                                        height: 0
                                    }, 500);

                                    /* Also hide the number of "So Hard..." reaction */
                                    $("#module-19 .hard_nb").delay(50).hide(300);

                                    /*
                                    * After a delay, show the "I'm getting better !" reaction image with nice
                                    * animation
                                    */
                                    $("#module-19 .better").css({"pointer-events": "none"})
                                        .delay(300).animate({
                                        top: -7.5,
                                        left: 35,
                                        height: 0
                                    }, 500);

                                    /* Also hide the number of "I'm getting better !" reaction */
                                    $("#module-19 .better_nb").delay(300).hide(300);

                                    /* After a delay, hide the "Easy !" reaction image with nice animation */
                                    $("#module-19 .easy").css({"pointer-events": "none"})
                                        .delay(600).animate({
                                        top: -7.5,
                                        left: -10,
                                        height: 0
                                    }, 500);

                                    /* Also hide the number of "Easy !" reaction */
                                    $("#module-19 .easy_nb").delay(600).hide(300);

                                    updateGroupImg();

                                    /* Show the reaction group image with nice animation */
                                    $(snapShotGroupImg2).show(0);
                                    $(snapShotGroupImg2).delay(500).animate({
                                        top: 0,
                                        left: 0,
                                        height: 20
                                    }, 300);

                                    /* Also show the number of total reaction */
                                    $("#module-19 .group_nb").delay(600).show(300);
                                }
                            }, 3000);
                        })

                        /* MOUSE OUT */
                        .mouseout(function GroupImgMouseOut() {

                            /* Reset timerGroupImg timer */
                            clearTimeout(timerGroupImgM19);

                            /* IF the mouse out before the reaction group hide */
                            if ($("#module-19 .easy").css("height") === "0px") {
                                /* Come back to the original size to inform that the image is mouse out */
                                $(this).animate({
                                    top: 0,
                                    left: 0,
                                    height: 20
                                }, 100);

                                /* Clear the animation queue to avoid image blinking */
                                $(this).clearQueue();
                            }
                        });

                    /* Management of the "Easy !" reaction */
                    $("#module-19 .easy")

                    /* MOUSE OVER */
                        .mouseover(function EasyMouseOver() {

                            /* Length of the text inside the toolbox to have a correct size */
                            var easyTxt = module19.getElementsByClassName("easy_txt")[0].innerText;
                            var easyTxtLength = easyTxt.length;

                            /* Modification of the toolbox width */
                            $("#module-19 .tooltip .tooltiptext").css({"width": easyTxtLength * 7 + "px", "left": "15px"});

                            /* Pointer modification to know that we can click or interact */
                            $(this).css({"cursor": "pointer"});

                            /* Widen a little to inform that the image is mouse over */
                            $(this).animate({
                                top: -25,
                                left: -30,
                                height: 40
                            }, 100);

                            /* Clear the animation queue to avoid image blinking */
                            $(this).clearQueue();
                        })

                        /* MOUSE OUT */
                        .mouseout(function EasyMouseOut() {

                            /* Come back to the original size to inform that the image is mouse out */
                            $(this).animate({
                                top: -15,
                                left: -20,
                                height: 20
                            }, 100);

                            /* Clear the animation queue to avoid image blinking */
                            $(this).clearQueue();
                        })

                        /* ON CLICK */
                        .click(function EasyClick() {

                            /* Get the number of "Easy !" reaction */
                            var easyNb = parseInt(module19.getElementsByClassName("easy_nb")[0].innerText);

                            /* Get the total number of reaction */
                            totalVoteM19 = parseInt(module19.getElementsByClassName("group_nb")[0].innerText);

                            /* IF there is no "Easy !" reaction, change the emoji in black and white */
                            if (easyNb === 0) {
                                $("#module-19 .easy").attr("src", "../blocks/like/img//easy.png");
                            }

                            /* It depends now of the previous reaction vote */
                            switch (reactionVotedM19) {
                                case Reactions.EASY:

                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'remove', userid: userId, cmid: 19, type: 1, vote: Reactions.EASY},

                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Decrement the number of "Easy !" of 1 */
                                    easyNb = easyNb - 1;

                                    /* Update the number of "Easy !" reaction */
                                    module19.getElementsByClassName("easy_nb")[0].innerText = easyNb;

                                    /* Update the text appearance to know that this is no longer the selected reaction */
                                    $("#module-19 .easy_nb").css({"font-weight": "normal", "color": "black"});

                                    /*
                                    * IF after the decrementation, the number of "Easy !" reaction is 0
                                    * THEN change the emoji in black and white
                                    */
                                    if (easyNb === 0) {
                                        $("#module-19 .easy").attr("src", "../blocks/like/img//easy_BW.png");
                                    }

                                    /* Update the value of total number reaction with an decrement of 1 */
                                    module19.getElementsByClassName("group_nb")[0].innerText = (totalVoteM19 - 1);

                                    /* Update the current reaction with "none reaction" */
                                    reactionVotedM19 = Reactions.NULL;
                                    break;

                                /* IF the previous was "I'm getting better !"*/
                                case Reactions.BETTER:

                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'update', userid: userId, cmid: 19, type: 1, vote: Reactions.EASY},

                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Increment the number of "Easy !" reaction of 1 */
                                    module19.getElementsByClassName("easy_nb")[0].innerText = (easyNb + 1);

                                    /* Update the text appearance to know that this is the selected reaction */
                                    $("#module-19 .easy_nb").css({"font-weight": "bold", "color": "#5585B6"});

                                    /*  Get the current number of "I'm getting better !" reaction and decrement it of 1 */
                                    var betterNb = parseInt(module19.getElementsByClassName("better_nb")[0].innerText) - 1;

                                    /* Update the value of "I'm getting better !" reaction */
                                    module19.getElementsByClassName("better_nb")[0].innerText = betterNb;

                                    /* Update the text appearance to know that this is no longer the selected reaction */
                                    $("#module-19 .better_nb").css({"font-weight": "normal", "color": "black"});

                                    /*
                                    * IF after the decrementation, the number of "I'm getting better !"
                                    * reaction is 0 THEN change the emoji in black and white
                                    */
                                    if (betterNb === 0) {
                                        $("#module-19 .better").attr("src", "../blocks/like/img//better_BW.png");
                                    }

                                    /* Update the current reation with "Easy !" */
                                    reactionVotedM19 = Reactions.EASY;
                                    break;

                                /* IF the previous was "So hard..."*/
                                case Reactions.HARD:

                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'update', userid: userId, cmid: 19, type: 1, vote: Reactions.EASY},

                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Increment the number of "Easy !" reaction of 1 */
                                    module19.getElementsByClassName("easy_nb")[0].innerText = (easyNb + 1);

                                    /* Update the text appearance to know that this is the selected reaction */
                                    $("#module-19 .easy_nb").css({"font-weight": "bold", "color": "#5585B6"});

                                    /*  Get the current number of "So hard..." reaction and decrement it of 1 */
                                    var hardNb = parseInt(module19.getElementsByClassName("hard_nb")[0].innerText) - 1;

                                    /* Update the value of "So hard..." reaction */
                                    module19.getElementsByClassName("hard_nb")[0].innerText = hardNb;

                                    /* Update the text appearance to know that this is no longer the selected reaction */
                                    $("#module-19 .hard_nb").css({"font-weight": "normal", "color": "black"});

                                    /*
                                    * IF after the decrementation, the number of "So hard..." reaction is 0
                                    * THEN change the emoji in black and white
                                    */
                                    if (hardNb === 0) {
                                        $("#module-19 .hard").attr("src", "../blocks/like/img//hard_BW.png");
                                    }

                                    /* Update the current reation with "Easy !" */
                                    reactionVotedM19 = Reactions.EASY;
                                    break;

                                /* IF there is no previous vote */
                                case Reactions.NULL:

                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'insert', userid: userId, cmid: 19, type: 1, vote: Reactions.EASY},
                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Increment the number of "Easy !" reaction of 1 */
                                    module19.getElementsByClassName("easy_nb")[0].innerText = (easyNb + 1);

                                    /* Update the text appearance to know that this is the selected reaction */
                                    $("#module-19 .easy_nb").css({"font-weight": "bold", "color": "#5585B6"});

                                    /* Update the value of total number reaction with an increment of 1 */
                                    module19.getElementsByClassName("group_nb")[0].innerText = (totalVoteM19 + 1);

                                    /* Update the current reation with "Easy !" */
                                    reactionVotedM19 = Reactions.EASY;
                                    break;
                            }
                        });

                    /* Management of the "I'm getting better !" reaction */
                    $("#module-19 .better")

                    /* MOUSE OVER */
                        .mouseover(function BetterMouseOver() {

                            /* Length of the text inside the toolbox to have a correct size */
                            var betterTxt = document.getElementById("module-19").getElementsByClassName("better_txt")[0].innerText;
                            var betterTxtLength = betterTxt.length;

                            /* Modification of the toolbox width */
                            $("#module-19 .tooltip .tooltiptext").css({
                                "width": betterTxtLength * 7 + "px",
                                "left": "40px"
                            });

                            /* Pointer modification to know that we can click or interact */
                            $(this).css({"cursor": "pointer"});

                            /* Widen a little to inform that the image is mouse over */
                            $(this).animate({
                                top: -25,
                                left: 15,
                                height: 40
                            }, 100);

                            /* Clear the animation queue to avoid image blinking */
                            $(this).clearQueue();
                        })

                        /* MOUSE OUT */
                        .mouseout(function BetterMouseOut() {

                            /* Come back to the original size to inform that the image is mouse out */
                            $(this).animate({
                                top: -15,
                                left: 25,
                                height: 20
                            }, 100);

                            /* Clear the animation queue to avoid image blinking */
                            $(this).clearQueue();
                        })

                        /* ON CLICK */
                        .click(function BetterClick() {

                            /* Get the number of "I'm getting better" reaction */
                            var betterNb = parseInt(module19.getElementsByClassName("better_nb")[0].innerText);

                            /* Get the total number of reaction */
                            totalVoteM19 = parseInt(module19.getElementsByClassName("group_nb")[0].innerText);

                            /* IF there is no "I'm getting better" reaction, change the emoji in black and white */
                            if (betterNb === 0) {
                                $("#module-19 .better").attr("src", "../blocks/like/img//better.png");
                            }

                            /* It depends now of the previous reaction vote */
                            switch (reactionVotedM19) {

                                case Reactions.BETTER:
                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'remove', userid: userId, cmid: 19, type: 1, vote: Reactions.BETTER},
                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Decrement the number of "I'm getting better !" of 1 */
                                    betterNb = betterNb - 1;

                                    /* Update the number of "I'm getting better !" reaction */
                                    module19.getElementsByClassName("better_nb")[0].innerText = betterNb;

                                    /* Update the text appearance to know that this is no longer the selected reaction */
                                    $("#module-19 .better_nb").css({"font-weight": "normal", "color": "black"});

                                    /*
                                    * IF after the decrementation, the number of "I'm getting better !" reaction
                                    * is 0 THEN change the emoji in black and white
                                    */
                                    if (betterNb === 0) {
                                        $("#module-19 .better").attr("src", "../blocks/like/img//better_BW.png");
                                    }

                                    /* Update the value of total number reaction with an decrement of 1 */
                                    module19.getElementsByClassName("group_nb")[0].innerText = (totalVoteM19 - 1);

                                    /* Update the current reaction with "none reaction" */
                                    reactionVotedM19 = Reactions.NULL;

                                    break;

                                /* IF the previous was "Easy !"*/
                                case Reactions.EASY:

                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'update', userid: userId, cmid: 19, type: 1, vote: Reactions.BETTER},
                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Increment the number of "I'm getting better !" reaction of 1 */
                                    module19.getElementsByClassName("better_nb")[0].innerText = (betterNb + 1);

                                    /* Update the text appearance to know that this is the selected reaction */
                                    $("#module-19 .better_nb").css({"font-weight": "bold", "color": "#5585B6"});

                                    /*  Get the current number of "Easy !" reaction and decrement it of 1 */
                                    var easyNb = parseInt(module19.getElementsByClassName("easy_nb")[0].innerText) - 1;

                                    /* Update the value of "Easy !" reaction */
                                    module19.getElementsByClassName("easy_nb")[0].innerText = easyNb;

                                    /* Update the text appearance to know that this is no longer the selected reaction */
                                    $("#module-19 .easy_nb").css({"font-weight": "normal", "color": "black"});

                                    /*
                                    * IF after the decrementation, the number of "Easy !" reaction is 0
                                    * THEN change the emoji in black and white
                                    */
                                    if (easyNb === 0) {
                                        $("#module-19 .easy").attr("src", "../blocks/like/img//easy_BW.png");
                                    }

                                    /* Update the current reation with "I'm getting better !" */
                                    reactionVotedM19 = Reactions.BETTER;
                                    break;

                                /* IF the previous was "So hard..."*/
                                case Reactions.HARD:

                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'update', userid: userId, cmid: 19, type: 1, vote: Reactions.BETTER},
                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Increment the number of "I'm getting better !" reaction of 1 */
                                    module19.getElementsByClassName("better_nb")[0].innerText = (betterNb + 1);

                                    /* Update the text appearance to know that this is the selected reaction */
                                    $("#module-19 .better_nb").css({"font-weight": "bold", "color": "#5585B6"});

                                    /*  Get the current number of "So hard..." reaction and decrement it of 1 */
                                    var hardNb = parseInt(module19.getElementsByClassName("hard_nb")[0].innerText) - 1;

                                    /* Update the value of "So hard..." reaction */
                                    module19.getElementsByClassName("hard_nb")[0].innerText = hardNb;

                                    /* Update the text appearance to know that this is no longer the selected reaction */
                                    $("#module-19 .hard_nb").css({"font-weight": "normal", "color": "black"});

                                    /*
                                    * IF after the decrementation, the number of "So hard..." reaction is 0
                                    * THEN change the emoji in black and white
                                    */
                                    if (hardNb === 0) {
                                        $("#module-19 .hard").attr("src", "../blocks/like/img//hard_BW.png");
                                    }

                                    /* Update the current reation with "I'm getting better !" */
                                    reactionVotedM19 = Reactions.BETTER;

                                    break;

                                /* IF there is no previous vote */
                                case Reactions.NULL:

                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'insert', userid: userId, cmid: 19, type: 1, vote: Reactions.BETTER},
                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Increment the number of "I'm getting better !" reaction of 1 */
                                    module19.getElementsByClassName("better_nb")[0].innerText = (betterNb + 1);

                                    /* Update the text appearance to know that this is the selected reaction */
                                    $("#module-19 .better_nb").css({"font-weight": "bold", "color": "#5585B6"});

                                    /* Update the value of total number reaction with an increment of 1 */
                                    module19.getElementsByClassName("group_nb")[0].innerText = (totalVoteM19 + 1);

                                    /* Update the current reation with "I'm getting better !" */
                                    reactionVotedM19 = Reactions.BETTER;

                                    break;
                            }
                        });

                    /* Management of the "So hard..." reaction */
                    $("#module-19 .hard")

                    /* MOUSE OVER */
                        .mouseover(function HardMouseOver() {

                            /* Length of the text inside the toolbox to have a correct size */
                            var hardTxt = module19.getElementsByClassName("hard_txt")[0].innerText;
                            var hardTxtLength = hardTxt.length;

                            /* Modification of the toolbox width */
                            $("#module-19 .tooltip .tooltiptext").css({"width": hardTxtLength * 7 + "px", "left": "105px"});

                            /* Pointer modification to know that we can click or interact */
                            $(this).css({"cursor": "pointer"});

                            /* Widen a little to inform that the image is mouse over */
                            $(this).animate({
                                top: -25,
                                left: 60,
                                height: 40
                            }, 100);

                            /* Clear the animation queue to avoid image blinking */
                            $(this).clearQueue();
                        })

                        /* MOUSE OUT */
                        .mouseout(function HardMouseOut() {

                            /* Come back to the original size to inform that the image is mouse out */
                            $(this).animate({
                                top: -15,
                                left: 70,
                                height: 20
                            }, 100);

                            /* Clear the animation queue to avoid image blinking */
                            $(this).clearQueue();
                        })

                        /* ON CLICK */
                        .click(function HardClick() {

                            /* Get the number of "So hard..." reaction */
                            var hardNb = parseInt(module19.getElementsByClassName("hard_nb")[0].innerText);

                            /* Get the total number of reaction */
                            totalVoteM19 = parseInt(module19.getElementsByClassName("group_nb")[0].innerText);

                            /* IF there is no "So hard... reaction, change the emoji in black and white */
                            if (hardNb === 0) {
                                $("#module-19 .hard").attr("src", "../blocks/like/img//hard.png");
                            }

                            /* It depends now of the previous reaction vote */
                            switch (reactionVotedM19) {

                                case Reactions.HARD:
                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'remove', userid: userId, cmid: 19, type: 1, vote: Reactions.HARD},

                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Decrement the number of So hard..." of 1 */
                                    hardNb = hardNb - 1;

                                    /* Update the number of "So hard..." reaction */
                                    module19.getElementsByClassName("hard_nb")[0].innerText = hardNb;

                                    /* Update the text appearance to know that this is no longer the selected reaction */
                                    $("#module-19 .hard_nb").css({"font-weight": "normal", "color": "black"});

                                    /*
                                    * IF after the decrementation, the number of "So hard..." reaction is 0
                                    * THEN change the emoji in black and white
                                    */
                                    if (hardNb === 0) {
                                        $("#module-19 .hard").attr("src", "../blocks/like/img//hard_BW.png");
                                    }

                                    /* Update the value of total number reaction with an decrement of 1 */
                                    module19.getElementsByClassName("group_nb")[0].innerText = (totalVoteM19 - 1);

                                    /* Update the current reaction with "none reaction" */
                                    reactionVotedM19 = Reactions.NULL;

                                    break;

                                /* IF the previous was "Easy !"*/
                                case Reactions.EASY:

                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'update', userid: userId, cmid: 19, type: 1, vote: Reactions.HARD},

                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Increment the number of "So hard..." reaction of 1 */
                                    module19.getElementsByClassName("hard_nb")[0].innerText = (hardNb + 1);

                                    /* Update the text appearance to know that this is the selected reaction */
                                    $("#module-19 .hard_nb").css({"font-weight": "bold", "color": "#5585B6"});

                                    /*  Get the current number of "Easy !" reaction and decrement it of 1 */
                                    var easyNb = parseInt(module19.getElementsByClassName("easy_nb")[0].innerText) - 1;

                                    /* Update the value of "Easy !" reaction */
                                    module19.getElementsByClassName("easy_nb")[0].innerText = easyNb;

                                    /* Update the text appearance to know that this is no longer the selected reaction */
                                    $("#module-19 .easy_nb").css({"font-weight": "normal", "color": "black"});

                                    /*
                                    * IF after the decrementation, the number of "Easy !" reaction is 0
                                    * THEN change the emoji in black and white
                                    */
                                    if (easyNb === 0) {
                                        $("#module-19 .easy").attr("src", "../blocks/like/img//easy_BW.png");
                                    }

                                    /* Update the current reation with "So hard..." */
                                    reactionVotedM19 = Reactions.HARD;
                                    break;

                                /* IF the previous was "I'm getting better !"*/
                                case Reactions.BETTER:

                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'update', userid: userId, cmid: 19, type: 1, vote: Reactions.HARD},

                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Increment the number of "So hard..." reaction of 1 */
                                    module19.getElementsByClassName("hard_nb")[0].innerText = (hardNb + 1);

                                    /* Update the text appearance to know that this is the selected reaction */
                                    $("#module-19 .hard_nb").css({"font-weight": "bold", "color": "#5585B6"});

                                    /*  Get the current number of "I'm getting better !" reaction and decrement it of 1 */
                                    var betterNb = parseInt(module19.getElementsByClassName("better_nb")[0].innerText) - 1;

                                    /* Update the value of "I'm getting better !" reaction */
                                    module19.getElementsByClassName("better_nb")[0].innerText = betterNb;

                                    /* Update the text appearance to know that this is no longer the selected reaction */
                                    $("#module-19 .better_nb").css({"font-weight": "normal", "color": "black"});

                                    /*
                                    * IF after the decrementation, the number of "I'm getting better !"
                                    * reaction is 0 THEN change the emoji in black and white
                                    */
                                    if (betterNb === 0) {
                                        $("#module-19 .better").attr("src", "../blocks/like/img//better_BW.png");
                                    }

                                    /* Update the current reation with "So hard..." */
                                    reactionVotedM19 = Reactions.HARD;
                                    break;

                                /* IF there is no previous vote */
                                case Reactions.NULL:

                                    /* [TODO] Comment */
                                    $.ajax({
                                        type: "POST",
                                        url: '../blocks/like/update_db.php',
                                        dataType: 'json',
                                        data: {func: 'insert', userid: userId, cmid: 19, type: 1, vote: Reactions.HARD},

                                        success: function(output) {
                                            /* eslint-disable no-console */
                                            console.log(output);
                                            /* eslint-enable no-console */
                                        }
                                    });

                                    /* Increment the number of "So hard..." reaction of 1 */
                                    module19.getElementsByClassName("hard_nb")[0].innerText = (hardNb + 1);

                                    /* Update the text appearance to know that this is the selected reaction */
                                    $("#module-19 .hard_nb").css({"font-weight": "bold", "color": "#5585B6"});

                                    /* Update the value of total number reaction with an increment of 1 */
                                    module19.getElementsByClassName("group_nb")[0].innerText = (totalVoteM19 + 1);

                                    /* Update the current reation with "So hard..." */
                                    reactionVotedM19 = Reactions.HARD;
                                    break;
                            }
                        });


                    /* Management of the reaction zone */
                    $("#module-19 .reactions")

                    /* MOUSE OVER */
                        .mouseover(function () {

                            /* The mouse is over the reaction zone */
                            reactionM19 = true;
                        })

                        /* MOUSE OUT */
                        .mouseout(function () {

                            /* The mouse is out the reaction zone */
                            reactionM19 = false;

                            /* Reset timerReactions timer */
                            clearTimeout(timerReactionsM19);

                            /* IF the mouse stay over at least 3 seconds... */
                            timerReactionsM19 = setTimeout(function () {

                                /* BUT that the mouse is note in the reaction zone */
                                if (!reactionM19) {

                                    /*
                                    * Disable the pointer events for each reactions images. This is to avoid a
                                    * bug, because this is possible  select a reaction during the hiding and
                                    * it create a bad comportment
                                    */

                                    /*
                                    * After a short delay, hide the "So Hard..." reaction image with nice
                                    * animation
                                    */

                                    $("#module-19 .hard").css({"pointer-events": "none"})
                                        .delay(50).animate({
                                        top: -7.5,
                                        left: 80,
                                        height: 0
                                    }, 500);

                                    /* Also hide the number of "So Hard..." reaction */
                                    $("#module-19 .hard_nb").delay(50).hide(300);

                                    /*
                                    * After a delay, show the "I'm getting better !" reaction image with nice
                                    * animation
                                    */
                                    $("#module-19 .better").css({"pointer-events": "none"})
                                        .delay(300).animate({
                                        top: -7.5,
                                        left: 35,
                                        height: 0
                                    }, 500);

                                    /* Also hide the number of "I'm getting better !" reaction */
                                    $("#module-19 .better_nb").delay(300).hide(300);

                                    /* After a delay, hide the "Easy !" reaction image with nice animation */
                                    $("#module-19 .easy").css({"pointer-events": "none"})
                                        .delay(600).animate({
                                        top: -7.5,
                                        left: -10,
                                        height: 0
                                    }, 500);

                                    /* Also hide the number of "Easy !" reaction */
                                    $("#module-19 .easy_nb").delay(600).hide(300);

                                    updateGroupImg();

                                    /* Show the reaction group image with nice animation */
                                    $("#module-19 .group_img").show(0).delay(500).animate({
                                        top: 0,
                                        left: 0,
                                        height: 20
                                    }, 300);

                                    /* Also show the number of total reaction */
                                    $("#module-19 .group_nb").delay(600).show(300);
                                }
                            }, 1000);
                        });
                }
            );
        }
    }
        ;
})
;
