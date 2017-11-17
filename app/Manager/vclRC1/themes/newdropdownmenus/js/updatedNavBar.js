/*
  Allows navbar to  move with screen as users scrolls.
  REPLACES topnav.js!
*/
$(document).bind("scroll", function() {
  if ($(document).scrollTop() > 72) {
    $("#site-navigation").addClass("fixed");
  } else {
    $("#site-navigation").removeClass("fixed");
  }
});
