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

//Show the data of the feedback on the profile
function showStats(data){

    $('#nbKnowledge').html(data.knowledge_count);
    $('#nbRequest').html(data.request_count);
    $('#nbReseau').html(data.network_size);
    $('#date_inscription').html(data.date_inscription);
}


function waitForElement(){
    if(typeof user !== "undefined"){
          //laod from API
          loadProfil(user.id);
          loadStats();
    }
    else{
        setTimeout(waitForElement, 250);
    }
}