$("#disconnectBtn").click(function(event) {
    $.post("php/controler/utilisateur.php",{
      function : 'signOut',
      data : {
         user : user.id
      }
   }, function(feedback){
      if(feedback.success){
         window.location = "login.html";
      }else{
         console.log('An error occured while destroying the session');
      }
   });
});