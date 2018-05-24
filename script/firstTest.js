console.log("Le script JS se lance bien !");

require(["jquery"], function($) {
    $(document).ready(function() {
      $("#module-19 .instancename").mouseover(function() {
        $(this).css({"font-weight": "bold"});
      })
      .mouseout(function() {
        $(this).css({"font-weight": "normal"});
      });

      $("#module-19 .instancename").append(" - <i>Texte en italique</i>");
    });
});
