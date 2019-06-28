
$("#btnChat").click(function(event) {
   $("#btnChat").addClass('d-none');
   $("#chat").hide();
   $("#chat").removeClass('d-none');
   $("#chat").fadeIn();
});

$("#closeChat").click(function(event) {
   $("#chat").fadeOut();
   $("#chat").addClass('d-none');
   $("#chat").show();
   $("#btnChat").removeClass('d-none');
});
