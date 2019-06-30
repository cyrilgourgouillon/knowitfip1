var idConvEnCours = false;

$(document).ready(function(){
  waitForElement();
});

function waitForElement(){
  if(typeof user !== "undefined"){
    loadReseau();
    loadNotification();
 }
 else{
    setTimeout(waitForElement, 250);
 }
}

function loadReseau(){
   $.post("php/controler/reseau.php",{
      function : 'getReseauOf',
      data : {
         id : user.id
      }
   }, function(feedback){
      if(feedback.success){
         showNetwork(feedback.data);
      }else{
         console.log('An error occured while loading the network');
      }
   });
}

function showNetwork(users){
   users.forEach(function(user){
      showUser(user);
   })
}

function showUser(user){
   html  = '';
   html += '<a href="javascript:void(0)" name="networkUser" data-id="'+user.id+'" class="list-group-item d-flex justify-content-between align-items-center px-1 py-2  list-group-item-action" style="border-radius: 0%; border-right: none; border-left: none;">';
   if(user.id){
      html += '<img height="20px" width="20px" class="img-profile mr-1 rounded-circle" src="user_pics/'+ user.id +'.jpg">';
   }else{
      html += '<img height="20px" width="20px" class="img-profile mr-1 rounded-circle" src="user_pics/default.jpg">';
   }
   html += '<span name="nameUserNetwork" class="mr-2 text-gray-600 small"> '+ user.pseudo +'</span></a>';
   $('#chatNetwork').append(html);
}

$(document).on('click','a[name="networkUser"]',function(){
   $(this).addClass('bg-secondary').find('span[name="nameUserNetwork"]').removeClass('text-gray-600').addClass('text-white');
   $(this).siblings().removeClass('bg-secondary').find('span[name="nameUserNetwork"]').removeClass('text-white').addClass('text-gray-600');
   showConversation($(this).attr('data-id'));
});

function showConversation(id){
   idConvEnCours = id;
   $.post("php/controler/message.php",{
      function : 'getConversation',
      data : {
         u1 : user.id,
         u2 : id
      }
   }, function(feedback){
      if(feedback.success){
         $("#chatMessages").empty();
         showMessages(feedback.data);
      }else{
         console.log('An error occured while loading the messages');
      }
   });
}

function showMessages(messages){
   messages.forEach(function(message){
      showMessage(message);
   })
}

function showMessage(message){
   html  = '';
   if(message.emetteur === user.id){
      html += '<div class="d-flex align-items-center mx-2 flex-row-reverse my-2">';
      if(message.emetteur){
         html += '<img height="30px" width="30px" class="img-profile mx-1 rounded-circle" src="user_pics/'+ message.emetteur +'.jpg">';
      }else{
         html += '<img height="30px" width="30px"class="img-profile mx-1 rounded-circle" src="user_pics/default.jpg">';
      }
      html += '<span class="px-3 py-1  bg-primary text-white " style="border-radius: 20px;" >'+ message.texte +'</span>';

   }else{
      html += '<div class="d-flex align-items-center mx-2 flex-row my-2">';
      if(message.emetteur){
         html += '<img height="30px" width="30px" class="img-profile mx-1 rounded-circle" src="user_pics/'+ message.emetteur +'.jpg">';
      }else{
         html += '<img height="30px" width="30px"class="img-profile mx-1 rounded-circle" src="user_pics/default.jpg">';
      }
      html += '<span class="px-3 py-1  bgMessageGrey " style="border-radius: 20px; color : #555" >'+ message.texte +'</span></div>';
   }
   $("#chatMessages").append(html);
   var element = document.getElementById("chatMessages");
   element.scrollTop = element.scrollHeight;
}

function sendNewMessage(){
   $.post("php/controler/message.php",{
      function : 'add',
      data : {
         message : $("#chatInput").val(),
         auteur : user.id,
         destinataire : idConvEnCours
      }
   }, function(feedback){
      if(feedback.success){
         showMessage({
            texte :  $("#chatInput").val(),
            emetteur : user.id
         });
         $("#chatInput").val("");
      }else{
         console.log('An error occured while loading the messages');
      }
   });
}

$('#chatInput').on("keyup", function(e) {
  if (e.keyCode == 13) {
    sendNewMessage();
 }
});

$("#chatSend").click(function(event) {
   sendNewMessage();
});

setInterval(checkNewMessage, 1500); 

function checkNewMessage(){
   if(idConvEnCours){
      $.post("php/controler/message.php",{
         function : 'getNewConversationMessage',
         data : {
            u1 : user.id,
            u2 : idConvEnCours
         }
      }, function(feedback){
         if(feedback.success){
            if(feedback.data){
               showMessages(feedback.data);
            }
         }else{
            console.log('An error occured while loading the messages');
         }
      });
   }
}


$("#btnChat").click(function(event) {
   var id = $('a[name="networkUser"].bg-secondary').attr('data-id');
   if(id){
     idConvEnCours = id;
  }
  $("#btnChat").addClass('d-none');
  $("#chat").hide();
  $("#chat").removeClass('d-none');
  $("#chat").fadeIn();
});

$("#closeChat").click(function(event) {
   idConvEnCours = false;
   $("#chat").fadeOut();
   $("#chat").addClass('d-none');
   $("#chat").show();
   $("#btnChat").removeClass('d-none');
});

