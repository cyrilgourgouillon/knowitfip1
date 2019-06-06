//On submit send server request
$("#submitButton").click(function(){
     $.post('php/controler/utilisateur.php',{
          function : 'signIn',
          data : {
               mail: $("#mail").val(),
               mdp : $("#password").val()
           }
     },function(feedback){
          getResponse(feedback);
     })
});

//Handle server response
function getResponse(feedback){
     if(feedback.success){
          window.location = "index.html";
     }else{
          unknowUser();
     }
}

//Print unknow people information
function unknowUser(){
     $("#mail").addClass('is-invalid');
     $("#password").addClass('is-invalid');
     $("#password").val('');
     $('#unknowUser').removeClass('d-none');
}


