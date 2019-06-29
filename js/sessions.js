$(document).ready(function(){
    waitForElement();
});

var sessions;

function loadPostulations(){
     $.post('php/controler/session.php',{
          function : 'getSessionByUser',
          data : {
               id: user.id
          }
     },function(feedback){
          if(feedback.success){
               sessions = feedback.data;
               showSessions(sessions);
          }else{
               console.log('An error occured while loading the data');
          }
     });
}

function showSessions(sessions){
     sessions.forEach(function(session){
          createSession(session);
     });
}

function createSession(session){

    var classes = ['badge-primary', 'badge-success', 'badge-warning', 'badge-danger'];

    var html = '';

    if(session.type === "knowledge"){
        html +='<div class="mt-3 card border-left-success shadow">';
   }else{
        html +='<div class="mt-3 card border-left-violet shadow">';
   }

   html +='<div class="card-body"><div class="row no-gutters align-items-center"><div class="col-xl-6">';
   html +='<div class="text-lg font-weight-bold ">' + session.titre + '</div>';
   html +='<div class="text-xs font-weight-bold text-gray-500 mb-1">Publié le ' + session.date + '</div>';
   html +='<div style="line-height: 1.1rem" class="mb-1">'+ session.description +'</div>';
   html +='<div class="mt-3 d-flex align-items-center justify-content-between"><div>';
   html +='<img width="30px" height="30px" class="img-profile rounded-circle mr-1" src="user_pics/' + session.idUser + '.jpg">';
   html +='<span class="mr-2 d-lg-inline text-gray-600 small">'+ session.pseudo +'</span></div><div>';

   session.tag.forEach(function(tag){
    var classe = classes[Math.floor(Math.random()*classes.length)];
    html += '<span class="badge mx-1 px-1 '+ classe +'">'+ tag.libelle +'</span>';
})

   html +='</div></div>   </div><div class="col-xl-6  px-3"><div class="row"><div class="col-md-6 d-flex justify-content-around align-items-center">';

   if(session.etat === 'En cours'){
      html += '<div class="badge-light badge-pill px-3 py-1 my-1">Session en cours...</div> ';
 }else{
      html += '<div class="badge-light badge-pill px-3 py-1 my-1">Session terminée</div> ';
 }

 html +='</div><div class="col-md-6 d-flex justify-content-around align-items-center">';
 if(session.etat === 'En cours'){
    
   html +=  '<a href="javascript:void(0)" onclick="termineSession('+ session.idSession +')" class="btn btn-primary py-1 my-1">Terminer la session</a>';
}else if(session.etat === 'Terminé'){
   html += '<a href="finsession.html?session='+ session.idSession +'"class="btn btn-info py-1 my-1">Consulter le bilan</a> ';
}
else if(session.etat === 'Attente note'){
     if(session.type === "knowledge"){
          if(user.id === session.candidat){
               html += '<a href="notation.html?session='+ session.idSession +'"class="btn btn-success py-1 my-1">Noter la session</a> ';
          }else{
               html += '<div class="badge-light badge-pill px-3 py-1 my-1">En attente d\'une note</div> ';
          }
     }else if(session.type === "request"){
          if(user.id === session.candidat){
               html += '<div class="badge-light badge-pill px-3 py-1 my-1">En attente d\'une note</div> ';
          }else{
               html += '<a href="notation.html?session='+ session.idSession +'"class="btn btn-success py-1 my-1">Noter la session</a> ';
          }
     }
}
html +='</div></div></div></div></div></div>';

$("#sessions-container").append(html);

}

function termineSession(idSession){

     var session = sessions.find(function(e){
          return e.idSession == idSession;
     });
     if(session.type === "knowledge"){
          if(user.id === session.candidat){
               window.location = "notation.html?session=" + idSession;
          }else{
               termineRequete(idSession);
          }
     }else if(session.type === "request"){
          if(user.id === session.candidat){
               termineRequete(idSession);
          }else{
               window.location = "notation.html?session=" + idSession;
          }
     }

}

function termineRequete(idSession){
     $.post('php/controler/session.php',{
          function : 'attenteSession',
          data : {
               id: idSession
          }
     },function(feedback){
          if(feedback.success){
                window.location = "sessions.html";
          }else{
               console.log('An error occured while ending the session');
          }
     });
}

function waitForElement(){
    if(typeof user !== "undefined"){
        loadPostulations();
   }
   else{
        setTimeout(waitForElement, 250);
   }
}