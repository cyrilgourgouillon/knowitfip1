//On submit send server request
$("#submitButton").click(function(){
     if(checkInputs())
          sendRequest();
});

function checkInputs(){
     //Check all input are not empty
     var success = true;
     [
          $("#prenom"),
          $("#nom"),
          $("#mail"),
          $("#password"),
          $("#secondPassword")
     ].forEach(function(e){
          if(e.val()==""){
               e.addClass('is-invalid');
               success = false;
          }else{
               e.removeClass('is-invalid');
          }
     });
     //Check checkobox is checked
     if(!$("#cgu").is(':checked')){
          $("#cgu").addClass('is-invalid');
          success = false;
     }else{
          $("#cgu").removeClass('is-invalid');
     }

     //test password 5 caracs minimum
     [$("#password"),$("#secondPassword")].forEach( function(e) {
          if(e.val().length <= 5){
               e.addClass('is-invalid');
               success = false;
          }else{
               e.removeClass('is-invalid');
          }
     });

     //test if password are the same
     if(success === true){
          if($("#password").val() !== $("#secondPassword").val()){
               $("#password").addClass('is-invalid');
               $("#secondPassword").addClass('is-invalid');
               success = false;
          }else{
               $("#password").removeClass('is-invalid');
               $("#secondPassword").removeClass('is-invalid');
          }
     }

     return success;
}

function sendRequest(){
     $.post('php/controler/utilisateur.php',{
          function : 'register',
          data : {
              prenom :  $("#prenom").val(),
              nom :  $("#nom").val(),
              mail : $("#mail").val(),
              password : $("#password").val()
           }
     },function(feedback){
          getResponse(feedback);
     })
}

//Handle server response
function getResponse(feedback){
     if(feedback.success){
          window.location = "login.html";
     }else{
          unknowUser(feedback.message);
     }
}

//Print unknow people information
function unknowUser(message){
     $('#unknowUser').removeClass('d-none');
     $('#erreurMessage').html(message);
}


