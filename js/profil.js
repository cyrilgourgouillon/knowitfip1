$(document).ready(function(){
    waitForElement();
});


//Load the user information from the profile
function loadProfil(){
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
                console.log("Une erreur est survenue lors du chargement du profil");
           */
        }
    );
}

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
function showUser(data){
     console.log($('#pseudo'));
    $('#pseudo').html(data.pseudo);
    $('#description').html(data.description);

    if(data.avatar == null)
        $('#inputImage').attr('src','user_pics/default.jpg');
    else
        $('#inputImage').attr('src',data.avatar);

   $('#credit').html(data.credit);

}


function waitForElement(){
    if(typeof user !== "undefined"){
        loadProfil();
        loadStats();
    }
    else{
        setTimeout(waitForElement, 250);
    }
}