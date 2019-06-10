var user;

class User{
     constructor(id, pseudo, avatar){
          this.id = id;
          this.pseudo = pseudo;
          this.avatar = avatar;
     }
}

function getCrtUser(){
     $.post('php/controler/utilisateur.php',{
          function : 'getBasicUserInfo',
          data : ['']
     },function(feedback){
          handleFeedback(feedback);
     });
}

function handleFeedback(feedback){
       if(feedback.success){
               user = new User(
                    feedback.data.id,
                    feedback.data.pseudo,
                    feedback.data.avatar
                    );
          }else{
               window.location = 'login.html';
          }
}

getCrtUser();


