$(document).ready(function(){
    waitForElement();
});

function loadCandidature(){
     $.post('php/controler/candidature.php',{
          function : 'getCommentCandidature',
          data : {
               id: findGetParameter('candidature')
          }
     },function(feedback){
          if(feedback.success){
               showComments(feedback.data.message, feedback.data.tmp_estime);
               loadProfil(feedback.data.candidat);
               loadStats(feedback.data.candidat);
          }else{
               console.log('An error occured while loading the data');
          }
     });
}

function showComments(message, tmp_estime){
	$("#message").html(message);
	$("#tmp_estime").html(tmp_estime);
}

//Load the user information from the profile
function loadStats(id){
    $.post('php/controler/utilisateur.php',{
        function : 'showStats',
        data : {
            id: id
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
        loadCandidature();
    }
    else{
        setTimeout(waitForElement, 250);
    }
}

/**
 * Get the Get param :: From stackoverfolow
 * https://stackoverflow.com/questions/5448545/how-to-retrieve-get-parameters-from-javascript
 * @param      {<type>}    parameterName  The parameter name
 * @return     {Function}  { description_of_the_return_value }
 */
function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
          tmp = item.split("=");
          if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}
