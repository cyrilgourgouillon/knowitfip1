$(document).ready(function(){
    waitForElement();
});

function loadUser(){
    $.post('php/controler/utilisateur.php',{
        function : 'showProfile',
          data : {
               id: user.id
           }
    },
        function (feedback) {
            showUser(feedback);
            /*
            if(feedback.success)
                showUser(feedback.data);
            else
                console.log("Une erreur est survenue lros du chargement du profil");
            */
        }
    );
}

function showUser(data){
    data.competences.foreach(function(e){
        addTag($("#listCompetence"), e);
    });
    data.competences_souhaitees.foreach(function(e){
        addTag($("#listCompetenceSouhaite"), e);
    });

    $('#inputPseudo').val(data.champ.pseudo);
    $('#inputDate').val(data.champ.date);
    $('#inputDescription').val(data.champ.description);
    $('#inputNom').val(data.champ.nom);
    $('#inputPrenom').val(data.champ.prenom);
    $('#inputImage').prop('src',data.champ.avatar);
}

function waitForElement(){
    if(typeof user !== "undefined"){
        loadUser();
    }
    else{
        setTimeout(waitForElement, 250);
    }
}