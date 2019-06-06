//On submit send server request
$("#submitButton").click(function(){
     if(checkInputs())
          sendRequest();
});

function checkInputs(){
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
     if(!$("#cgu").is(':checked')){
          $("#cgu").addClass('is-invalid');
          success = false;
     }else{
          $("#cgu").removeClass('is-invalid');
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
              password : $("#password").val(),
              secondPassword : $("#secondPassword").val()
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
          unknowUser();
     }
}

//Print unknow people information
function unknowUser(){
     $('#unknowUser').removeClass('d-none');
}


