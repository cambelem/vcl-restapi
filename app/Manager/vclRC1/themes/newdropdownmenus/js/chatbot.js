$(document).ready(function(){
  windowHeight = $(window).height();
  windowWidth = $(window).width();
//  $("#openchat").width(windowHeight *.05);
//  $("#openchat").height(windowHeight *.05);
  $(window).resize(function() {
   windowHeight = $(window).height();
   windowWidth = $(window).width();
//   $("#openchat").width(windowHeight *.05);
//   $("#openchat").height(windowHeight *.05);
  })

  $("#chatIcon").fadeOut(175, function() {
   $("#openchat").css({"color":"white", "background": "#333", "border-radius": "1.2rem"});

    $(this).fadeIn(175);
  });
  $("#chatWrapper").fadeToggle();

  $("#openchat").click(function(){
   if ($("#chatWrapper").is(":visible")) {
     $("#chatIcon").fadeOut(175, function() {
	      $("#openchat").css({"color":"#333", "background": "white"});
        $(this).fadeIn(175);
     });
  } else {
     $("#chatIcon").fadeOut(175, function() {
			 $("#openchat").css({"color":"white", "background": "#333", "border-radius": "1.2rem"});
       $(this).fadeIn(175);
     });
  }
  $("#chatWrapper").fadeToggle();
 });

 $("#closeChatbot").click(function(){
   $("#chatIcon").fadeOut(175, function() {
      $("#openchat").css({"color":"#333", "background": "white"});
      $(this).fadeIn(175);
   });
   $("#chatWrapper").fadeToggle();
 });
});
