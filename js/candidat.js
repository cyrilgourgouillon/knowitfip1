$(document).ready(function(){
    waitForElement();
});

function loadCandidats(){
     $.post('php/controler/candidature.php',{
          function : 'getCandidatureByPost',
          data : {
               id: findGetParameter('post')
          }
     },function(feedback){
          if(feedback.success){
               showCandidats(feedback.data);
          }else{
               console.log('An error occured while loading the data');
          }
     });
}

function showCandidats(candidats){
	candidats.forEach(function(candidat){
		createProfil(candidat);
	})
}

function createProfil(candidat){
	var html = '<div class="col-xl-4"><div class="card shadow mb-4"><div class="card-body"><div class="text-center">';
    html += '<img width="150px" height="150px" class="img-profile rounded-circle" src="' + candidat.avatar + '"><h3 class="mt-3 mb-0">';
    html +=  candidat.pseudo + '</h3><div class="text-gray-500"><div>';
    html += candidat.knowledge_count + ' knowledges • ' + candidat.request_count + ' requests</div><div>';
    html +=  candidat.age + ' ans</div></div></div><div class="mt-4 px-1">';
    html +=  candidat.description + '</div> <div class="mt-3 px-1">';

    candidat.tag.forEach(function(tag){
    	html += '<div class="mb-2"><span class="small ">' + tag.libelle + ' • ' + tag.points_experience + '  exp</span><div class="progress mb-4">';
	    html += '<div class="progress-bar bg-warning" role="progressbar" style="width: ' + tag.points_experience/10 + '%" aria-valuenow="' + tag.points_experience/10 + '" aria-valuemin="0" aria-valuemax="100"></div></div></div>';
    });

	html += '</div> <div class="text-center">';
	html += '<button  onclick="window.location = \'profilpost.html?candidature='+ candidat.idCandidature +'\';" style="width: 3rem; height: 3rem;" class="btn rounded-circle btn-primary border-0 text-white" id="newBtn">';
	html += '<i class="mt-1 fas fa-chevron-down"></i></button></div></div></div></div>';
	$("#candidatureContainer").append(html);
}

function waitForElement(){
    if(typeof user !== "undefined"){
        loadCandidats();
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
