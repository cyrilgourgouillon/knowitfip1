$(document).ready(function(){
    waitForElement();
});



//Load the user information from the profile
function loadStats(){
    $.post('php/controler/utilisateur.php',{
        function : 'showStats',
        data : {
            id: user.id
        }
    },
        function (feedback) {
            if(feedback.success)
                showStats(feedback.data);
            else
                console.log("Une erreur est survenue lors du chargement du profil");
        }
    );
}


function loadUserReseau(){
    $.post("php/controler/reseau.php",{
      function : 'getReseauOf',
      data : {
         id : user.id
      }
   }, function(feedback){
      if(feedback.success){
         showUserNetworks(feedback.data);
      }else{
         console.log('An error occured while loading the network');
      }
   });
}

function showUserNetworks(users){
   users.forEach(function(user){
      showUserNetwork(user);
   })
}

function showUserNetwork(user){
   html  = '';
   html  += '<li class="list-group-item">';
   html  += '<img width="35px" height="35px" class="img-profile rounded-circle mr-2" src="user_pics/'+ user.id +'.jpg">';
   html  += '<a href="#">'+user.pseudo+'</a></li>';

   $("#reseau").append(html);

}


//Show the data of the feedback on the profile
function showStats(data){

    $('#nbKnowledge').html(data.knowledge_count);
    $('#nbRequest').html(data.request_count);
    $('#nbReseau').html(data.network_size);
    $('#date_inscription').html(data.date_inscription);
}

function waitForElement(){
    if(typeof user !== "undefined"){

        /**
        * ALL PAGES EXECUTE
        */
        loadReseau();
        loadMessages();
        loadNotification();

        /**
         * THIS PAGE EXECUTE
        */

        loadProfil(user.id);
        loadStats();
        loadUserReseau();
     
    }else{
        setTimeout(waitForElement, 250);
    }
}
