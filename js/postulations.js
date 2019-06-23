$(document).ready(function(){
    waitForElement();
});

function loadPostulations(){
     $.post('php/controler/candidature.php',{
          function : 'getCandidatureByUser',
          data : {
               id: user.id
          }
     },function(feedback){
          if(feedback.success){
               showPostulations(feedback.data);
          }else{
               console.log('An error occured while loading the data');
          }
     });
}

function showPostulations(postulations){
     postulations.forEach(function(postulation){
          createPostulation(postulation);
     });
}

function createPostulation(postulation){

    var classes = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger'];

    var html = '';

    if(postulation.type === "knowledge"){
        html +='<div class="mt-3 card border-left-success shadow">';
    }else{
        html +='<div class="mt-3 card border-left-violet shadow">';
    }

    html +='<div class="card-body"><div class="row no-gutters align-items-center"><div class="col-xl-6">';
    html +='<div class="text-lg font-weight-bold ">' + postulation.titre + '</div>';
    html +='<div class="text-xs font-weight-bold text-gray-500 mb-1">Publié le ' + postulation.date + '</div>';
    html +='<div style="line-height: 1.1rem" class="mb-1">'+ postulation.description +'</div>';
    html +='<div class="mt-3 d-flex align-items-center justify-content-between"><div>';
    html +='<img width="30px" height="30px" class="img-profile rounded-circle mr-1" src="user_pics/' + postulation.idUser + '.jpg">';
    html +='<span class="mr-2 d-lg-inline text-gray-600 small">'+ postulation.pseudo +'</span></div><div>';

    postulation.tag.forEach(function(tag){
              var classe = classes[Math.floor(Math.random()*classes.length)];
              html += '<span class="badge mx-1 px-1 '+ classe +'">'+ tag.libelle +'</span>';
    })

    html +='</div></div>   </div><div class="col-xl-6  px-3"><div class="row"><div class="col-md-6 d-flex justify-content-around align-items-center">';
    if(postulation.type === "knowledge"){
    html +='<button onclick="window.location=\'post.html?post='+ postulation.idPost +'\'" class="btn rounded-circle chevron-rounded border-0 text-success" id="newBtn"><i class="fas fa-chevron-right"></i></button>';
    }else{
        html +='<button onclick="window.location=\'post.html?post='+ postulation.idPost +'\'" class="btn rounded-circle chevron-rounded border-0 violet" id="newBtn"><i class="fas fa-chevron-right"></i></button>';
    }
    html +='</div><div class="col-md-6 d-flex justify-content-around align-items-center">';
    if(postulation.etat === 'En attente'){
        html += '<div class="badge-light badge-pill px-3 py-1">En attente de réponse...</div> ';
     }else if(postulation.etat === 'Refusé'){
        html += '<div class="badge-danger badge-pill px-3 py-1">Vous n\'avez pas été choisis</div> ';
     }else if(postulation.etat === 'Validé'){
        html += '<div class="badge-info badge-pill px-3 py-1">Postulation finalisée</div> ';
     }else if(postulation.etat === 'Annulé'){
        html += '<div class="badge-warning badge-pill px-3 py-1">Postulation annulée</div> ';
     }else if(postulation.etat === 'Accepté'){
         html += '<a href="confirmation.html?candidature='+ postulation.idCandid +'" class="btn btn-success"> Vous avez été choisis !</a>';
     }
    html +='</div></div></div></div></div></div>';

   $("#postulations-container").append(html);

}

function waitForElement(){
    if(typeof user !== "undefined"){
        loadPostulations();
    }
    else{
        setTimeout(waitForElement, 250);
    }
}